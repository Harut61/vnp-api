<?php

namespace App\Enums;

class FtpProtocolEnum extends BasicEnum
{
    const FTP = 'ftp';
    const SFTP = 'sftp';
    const IMPLICIT_FTP = 'implicit_ftp';
    const EXPLICIT_FTP = 'explicit_ftp';
}
