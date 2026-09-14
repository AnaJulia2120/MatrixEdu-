<?php
/**
 * MatrixEdu - Notificações por e-mail
 *
 * COMO USAR:
 * require_once __DIR__ . '/notificar_email.php';
 *
 * notificarEmail(
 *     'destinatario@exemplo.com',
 *     'Assunto do e-mail',
 *     '<h2>Olá!</h2><p>Mensagem...</p>'
 * );
 *
 * IMPORTANTE:
 * 1. Este arquivo usa a função mail() nativa do PHP.
 * 2. Para funcionar em hospedagem, o servidor precisa ter o envio de e-mail
 *    configurado (SMTP/MTA).
 * 3. Não coloque senha de e-mail neste arquivo.
 */

function notificarEmail(string $destinatario, string $assunto, string $html): bool
{
    if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Troque somente estes dois dados.
    $emailRemetente = 'noreply@matrixedu.com';
    $nomeRemetente  = 'MatrixEdu';

    $assunto = '=?UTF-8?B?' . base64_encode($assunto) . '?=';

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $nomeRemetente . " <" . $emailRemetente . ">\r\n";
    $headers .= "Reply-To: " . $emailRemetente . "\r\n";
    $headers .= "X-Mailer: MatrixEdu\r\n";

    return @mail($destinatario, $assunto, $html, $headers);
}

/**
 * E-mail enviado quando o administrador libera ou bloqueia uma licença.
 */
function emailLicenca(string $nome, string $email, string $status): bool
{
    $liberada = ($status === 'liberada');

    $assunto = $liberada
        ? 'Sua licença do MatrixEdu foi liberada'
        : 'Sua licença do MatrixEdu foi bloqueada';

    $titulo = $liberada
        ? 'Licença liberada!'
        : 'Licença bloqueada';

    $mensagem = $liberada
        ? 'Sua conta de professor(a) foi liberada pelo administrador. Você já pode acessar o MatrixEdu.'
        : 'Sua licença de acesso ao MatrixEdu foi bloqueada pelo administrador. Entre em contato com a administração caso isso tenha sido um engano.';

    $cor = $liberada ? '#15803d' : '#b91c1c';

    $html = '
    <!doctype html>
    <html lang="pt-BR">
    <head><meta charset="UTF-8"></head>
    <body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#172033;">
      <div style="max-width:600px;margin:30px auto;background:#fff;border-radius:12px;padding:30px;box-shadow:0 3px 15px rgba(0,0,0,.08);">
        <h1 style="margin-top:0;color:#172033;">MatrixEdu</h1>
        <div style="border-left:5px solid '.$cor.';padding-left:15px;">
          <h2>'.$titulo.'</h2>
        </div>
        <p>Olá, <strong>'.htmlspecialchars($nome, ENT_QUOTES, 'UTF-8').'</strong>.</p>
        <p>'.$mensagem.'</p>
        <p style="margin-top:30px;color:#667085;font-size:13px;">
          Este é um aviso automático do sistema MatrixEdu.
        </p>
      </div>
    </body>
    </html>';

    return notificarEmail($email, $assunto, $html);
}
