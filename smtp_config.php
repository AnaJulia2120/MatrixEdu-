<?php
/**
 * CONFIGURAÇÃO DO E-MAIL DO MATRIXEDU
 *
 * Preencha os dados da conta que será usada para enviar os avisos.
 *
 * Exemplos:
 *
 * Gmail:
 * host       = smtp.gmail.com
 * port       = 587
 * encryption = tls
 *
 * Outlook/Microsoft 365:
 * host       = smtp.office365.com
 * port       = 587
 * encryption = tls
 *
 * E-mail da hospedagem:
 * use os dados SMTP fornecidos pelo seu provedor/cPanel.
 *
 * IMPORTANTE:
 * - Para Gmail, normalmente é necessário usar uma SENHA DE APP,
 *   não a senha normal da conta.
 * - Nunca publique este arquivo com a senha em um repositório público.
 */

function matrixedu_smtp_config(): array
{
    return [
        'host'       => 'smtp.seudominio.com.br',
        'port'       => 587,
        'encryption' => 'tls', // tls, ssl ou none

        'username'   => 'noreply@seudominio.com.br',
        'password'   => 'COLOQUE_A_SENHA_AQUI',

        'from_email' => 'noreply@seudominio.com.br',
        'from_name'  => 'MatrixEdu',
    ];
}
