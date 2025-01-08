<?php

namespace App\Enums;

enum OrderStatus: string
{
    case OC = 'OC';
    case PRP = 'PRP'; 
    case RFP = 'RFP';
    case P = 'P';
    case SHP = 'SHP'; 
    case PD = 'PD'; 

    /**
     * Enum sıralama düzeni.
     */
    public static function order(): array
    {
        return [
            self::OC->value,
            self::PRP->value,
            self::RFP->value,
            self::P->value,
            self::SHP->value,
            self::PD->value,
        ];
    }

    /**
     * İnsan tarafından okunabilir bir Türkçe etiket döndür.
     */
    public function label(): string
    {
        return match ($this) {
            self::OC => 'Sipariş Oluşturuldu',
            self::PRP => 'Hazırlanıyor',
            self::RFP => 'Teslimata Hazır',
            self::P => 'Üretimde',
            self::SHP => 'Kargo',
            self::PD => 'Teslim Edildi',
        };
    }
}
