<?php
/**
 * MatrixEdu - envio de e-mails por SMTP
 *
 * Configure os dados em smtp_config.php.
 * Não coloque este arquivo em uma pasta pública diferente da aplicação.
 */

require_once __DIR__ . '/smtp_config.php';

function matrixedu_smtp_send(string $to, string $subject, string $html): bool
{
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $cfg = matrixedu_smtp_config();

    $host = $cfg['host'];
    $port = (int)$cfg['port'];
    $user = $cfg['username'];
    $pass = $cfg['password'];
    $from = $cfg['from_email'];
    $fromName = $cfg['from_name'];

    $remote = ($cfg['encryption'] === 'ssl')
        ? 'ssl://' . $host . ':' . $port
        : 'tcp://' . $host . ':' . $port;

    $errno = 0;
    $errstr = '';
    $socket = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
    if (!$socket) {
        return false;
    }

    stream_set_timeout($socket, 15);

    $read = function() use ($socket): string {
        $data = '';
        while (($line = fgets($socket, 515)) !== false) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $data;
    };

    $command = function(string $cmd, array $accepted) use ($socket, $read): bool {
        fwrite($socket, $cmd . "\r\n");
        $response = $read();
        $code = (int)substr(trim($response), 0, 3);
        return in_array($code, $accepted, true);
    };

    $greeting = $read();
    if ((int)substr(trim($greeting), 0, 3) !== 220) {
        fclose($socket);
        return false;
    }

    $helo = $_SERVER['SERVER_NAME'] ?? 'localhost';
    if (!$command("EHLO " . $helo, [250])) {
        fclose($socket);
        return false;
    }

    if ($cfg['encryption'] === 'tls') {
        if (!$command('STARTTLS', [220])) {
            fclose($socket);
            return false;
        }

        $crypto = @stream_socket_enable_crypto(
            $socket,
            true,
            STREAM_CRYPTO_METHOD_TLS_CLIENT
        );

        if ($crypto !== true) {
            fclose($socket);
            return false;
        }

        if (!$command("EHLO " . $helo, [250])) {
            fclose($socket);
            return false;
        }
    }

    if ($user !== '') {
        if (!$command('AUTH LOGIN', [334])) {
            fclose($socket);
            return false;
        }
        if (!$command(base64_encode($user), [334])) {
            fclose($socket);
            return false;
        }
        if (!$command(base64_encode($pass), [235])) {
            fclose($socket);
            return false;
        }
    }

    if (!$command('MAIL FROM:<' . $from . '>', [250])) {
        fclose($socket);
        return false;
    }

    if (!$command('RCPT TO:<' . $to . '>', [250, 251])) {
        fclose($socket);
        return false;
    }

    fwrite($socket, "DATA\r\n");
    $dataResponse = $read();
    if ((int)substr(trim($dataResponse), 0, 3) !== 354) {
        fclose($socket);
        return false;
    }

    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $safeFromName = mb_encode_mimeheader($fromName, 'UTF-8', 'B', "\r\n");

    $headers = [];
    $headers[] = 'Date: ' . date(DATE_RFC2822);
    $headers[] = 'From: ' . $safeFromName . ' <' . $from . '>';
    $headers[] = 'To: <' . $to . '>';
    $headers[] = 'Subject: ' . $encodedSubject;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'Content-Transfer-Encoding: 8bit';
    $headers[] = 'X-Mailer: MatrixEdu';

    // Evita que uma linha iniciada por "." encerre o DATA prematuramente.
    $html = preg_replace('/^\./m', '..', $html);

    $message = implode("\r\n", $headers) . "\r\n\r\n" . $html . "\r\n.\r\n";
    fwrite($socket, $message);

    $sentResponse = $read();
    $sent = in_array((int)substr(trim($sentResponse), 0, 3), [250], true);

    @fwrite($socket, "QUIT\r\n");
    fclose($socket);

    return $sent;
}

function matrixedu_enviar_email_licenca(
    string $nome,
    string $email,
    string $status
): bool {
    $liberada = $status === 'liberada';

    $assunto = $liberada
        ? 'MatrixEdu - sua licença foi liberada'
        : 'MatrixEdu - sua licença foi bloqueada';

    $titulo = $liberada ? 'Licença liberada!' : 'Licença bloqueada';
    $cor = $liberada ? '#15803d' : '#b91c1c';

    $mensagem = $liberada
        ? 'O administrador liberou seu acesso ao MatrixEdu. Você já pode entrar no sistema.'
        : 'O administrador bloqueou sua licença de acesso ao MatrixEdu. Entre em contato com a administração se precisar de ajuda.';

    $nomeEsc = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
    $mensagemEsc = htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8');

    $html = '<!doctype html>
<html lang="pt-BR">
<head><meta charset="UTF-8"></head>
<body style="margin:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#172033;">
  <div style="max-width:600px;margin:30px auto;background:#ffffff;padding:32px;border-radius:12px;">
    <h1 style="margin:0 0 24px;">MatrixEdu</h1>
    <div style="border-left:5px solid ' . $cor . ';padding:4px 0 4px 15px;">
      <h2 style="margin:0;">' . $titulo . '</h2>
    </div>
    <p>Olá, <strong>' . $nomeEsc . '</strong>.</p>
    <p>' . $mensagemEsc . '</p>
    <p style="margin-top:30px;font-size:13px;color:#667085;">
      Esta é uma mensagem automática do MatrixEdu. Não responda diretamente a este e-mail.
    </p>
  </div>
</body>
</html>';

    return matrixedu_smtp_send($email, $assunto, $html);
}
