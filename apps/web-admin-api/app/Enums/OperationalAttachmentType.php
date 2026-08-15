<?php

declare(strict_types=1);

namespace App\Enums;

enum OperationalAttachmentType: string
{
    case Disbursement = 'disbursement';
    case TransferProof = 'transfer_proof';

    public function label(): string
    {
        return match ($this) {
            self::Disbursement => 'Foto Pencairan',
            self::TransferProof => 'Foto Bukti Transfer',
        };
    }
}
