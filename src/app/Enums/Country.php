<?php

namespace Fereydooni\Shopping\App\Enums;

enum Country: string
{
    case UNITED_STATES = 'US';
    case CANADA = 'CA';
    case UNITED_KINGDOM = 'GB';
    case GERMANY = 'DE';
    case FRANCE = 'FR';
    case ITALY = 'IT';
    case SPAIN = 'ES';
    case NETHERLANDS = 'NL';
    case BELGIUM = 'BE';
    case SWITZERLAND = 'CH';
    case AUSTRIA = 'AT';
    case SWEDEN = 'SE';
    case NORWAY = 'NO';
    case DENMARK = 'DK';
    case FINLAND = 'FI';
    case POLAND = 'PL';
    case CZECH_REPUBLIC = 'CZ';
    case HUNGARY = 'HU';
    case ROMANIA = 'RO';
    case BULGARIA = 'BG';
    case GREECE = 'GR';
    case PORTUGAL = 'PT';
    case IRELAND = 'IE';
    case LUXEMBOURG = 'LU';
    case MALTA = 'MT';
    case CYPRUS = 'CY';
    case ESTONIA = 'EE';
    case LATVIA = 'LV';
    case LITHUANIA = 'LT';
    case SLOVENIA = 'SI';
    case SLOVAKIA = 'SK';
    case CROATIA = 'HR';
    case AUSTRALIA = 'AU';
    case NEW_ZEALAND = 'NZ';
    case JAPAN = 'JP';
    case SOUTH_KOREA = 'KR';
    case CHINA = 'CN';
    case INDIA = 'IN';
    case BRAZIL = 'BR';
    case MEXICO = 'MX';
    case ARGENTINA = 'AR';
    case CHILE = 'CL';
    case COLOMBIA = 'CO';
    case PERU = 'PE';
    case VENEZUELA = 'VE';
    case URUGUAY = 'UY';
    case PARAGUAY = 'PY';
    case BOLIVIA = 'BO';
    case ECUADOR = 'EC';
    case GUYANA = 'GY';
    case SURINAME = 'SR';
    case SOUTH_AFRICA = 'ZA';
    case NIGERIA = 'NG';
    case KENYA = 'KE';
    case GHANA = 'GH';
    case ETHIOPIA = 'ET';
    case TANZANIA = 'TZ';
    case UGANDA = 'UG';
    case MOROCCO = 'MA';
    case ALGERIA = 'DZ';
    case TUNISIA = 'TN';
    case EGYPT = 'EG';
    case ISRAEL = 'IL';
    case TURKEY = 'TR';
    case IRAN = 'IR';
    case IRAQ = 'IQ';
    case SAUDI_ARABIA = 'SA';
    case UAE = 'AE';
    case QATAR = 'QA';
    case KUWAIT = 'KW';
    case BAHRAIN = 'BH';
    case OMAN = 'OM';
    case YEMEN = 'YE';
    case JORDAN = 'JO';
    case LEBANON = 'LB';
    case SYRIA = 'SY';
    case RUSSIA = 'RU';
    case UKRAINE = 'UA';
    case BELARUS = 'BY';
    case KAZAKHSTAN = 'KZ';
    case UZBEKISTAN = 'UZ';
    case TURKMENISTAN = 'TM';
    case KYRGYZSTAN = 'KG';
    case TAJIKISTAN = 'TJ';
    case AFGHANISTAN = 'AF';
    case PAKISTAN = 'PK';
    case BANGLADESH = 'BD';
    case SRI_LANKA = 'LK';
    case NEPAL = 'NP';
    case BHUTAN = 'BT';
    case MYANMAR = 'MM';
    case THAILAND = 'TH';
    case VIETNAM = 'VN';
    case LAOS = 'LA';
    case CAMBODIA = 'KH';
    case MALAYSIA = 'MY';
    case SINGAPORE = 'SG';
    case INDONESIA = 'ID';
    case PHILIPPINES = 'PH';
    case TAIWAN = 'TW';
    case HONG_KONG = 'HK';
    case MACAU = 'MO';
    case MONGOLIA = 'MN';
    case NORTH_KOREA = 'KP';

    public function name(): string
    {
        return match ($this) {
            self::UNITED_STATES => 'United States',
            self::CANADA => 'Canada',
            self::UNITED_KINGDOM => 'United Kingdom',
            self::GERMANY => 'Germany',
            self::FRANCE => 'France',
            self::ITALY => 'Italy',
            self::SPAIN => 'Spain',
            self::NETHERLANDS => 'Netherlands',
            self::BELGIUM => 'Belgium',
            self::SWITZERLAND => 'Switzerland',
            self::AUSTRIA => 'Austria',
            self::SWEDEN => 'Sweden',
            self::NORWAY => 'Norway',
            self::DENMARK => 'Denmark',
            self::FINLAND => 'Finland',
            self::POLAND => 'Poland',
            self::CZECH_REPUBLIC => 'Czech Republic',
            self::HUNGARY => 'Hungary',
            self::ROMANIA => 'Romania',
            self::BULGARIA => 'Bulgaria',
            self::GREECE => 'Greece',
            self::PORTUGAL => 'Portugal',
            self::IRELAND => 'Ireland',
            self::LUXEMBOURG => 'Luxembourg',
            self::MALTA => 'Malta',
            self::CYPRUS => 'Cyprus',
            self::ESTONIA => 'Estonia',
            self::LATVIA => 'Latvia',
            self::LITHUANIA => 'Lithuania',
            self::SLOVENIA => 'Slovenia',
            self::SLOVAKIA => 'Slovakia',
            self::CROATIA => 'Croatia',
            self::AUSTRALIA => 'Australia',
            self::NEW_ZEALAND => 'New Zealand',
            self::JAPAN => 'Japan',
            self::SOUTH_KOREA => 'South Korea',
            self::CHINA => 'China',
            self::INDIA => 'India',
            self::BRAZIL => 'Brazil',
            self::MEXICO => 'Mexico',
            self::ARGENTINA => 'Argentina',
            self::CHILE => 'Chile',
            self::COLOMBIA => 'Colombia',
            self::PERU => 'Peru',
            self::VENEZUELA => 'Venezuela',
            self::URUGUAY => 'Uruguay',
            self::PARAGUAY => 'Paraguay',
            self::BOLIVIA => 'Bolivia',
            self::ECUADOR => 'Ecuador',
            self::GUYANA => 'Guyana',
            self::SURINAME => 'Suriname',
            self::SOUTH_AFRICA => 'South Africa',
            self::NIGERIA => 'Nigeria',
            self::KENYA => 'Kenya',
            self::GHANA => 'Ghana',
            self::ETHIOPIA => 'Ethiopia',
            self::TANZANIA => 'Tanzania',
            self::UGANDA => 'Uganda',
            self::MOROCCO => 'Morocco',
            self::ALGERIA => 'Algeria',
            self::TUNISIA => 'Tunisia',
            self::EGYPT => 'Egypt',
            self::ISRAEL => 'Israel',
            self::TURKEY => 'Turkey',
            self::IRAN => 'Iran',
            self::IRAQ => 'Iraq',
            self::SAUDI_ARABIA => 'Saudi Arabia',
            self::UAE => 'United Arab Emirates',
            self::QATAR => 'Qatar',
            self::KUWAIT => 'Kuwait',
            self::BAHRAIN => 'Bahrain',
            self::OMAN => 'Oman',
            self::YEMEN => 'Yemen',
            self::JORDAN => 'Jordan',
            self::LEBANON => 'Lebanon',
            self::SYRIA => 'Syria',
            self::RUSSIA => 'Russia',
            self::UKRAINE => 'Ukraine',
            self::BELARUS => 'Belarus',
            self::KAZAKHSTAN => 'Kazakhstan',
            self::UZBEKISTAN => 'Uzbekistan',
            self::TURKMENISTAN => 'Turkmenistan',
            self::KYRGYZSTAN => 'Kyrgyzstan',
            self::TAJIKISTAN => 'Tajikistan',
            self::AFGHANISTAN => 'Afghanistan',
            self::PAKISTAN => 'Pakistan',
            self::BANGLADESH => 'Bangladesh',
            self::SRI_LANKA => 'Sri Lanka',
            self::NEPAL => 'Nepal',
            self::BHUTAN => 'Bhutan',
            self::MYANMAR => 'Myanmar',
            self::THAILAND => 'Thailand',
            self::VIETNAM => 'Vietnam',
            self::LAOS => 'Laos',
            self::CAMBODIA => 'Cambodia',
            self::MALAYSIA => 'Malaysia',
            self::SINGAPORE => 'Singapore',
            self::INDONESIA => 'Indonesia',
            self::PHILIPPINES => 'Philippines',
            self::TAIWAN => 'Taiwan',
            self::HONG_KONG => 'Hong Kong',
            self::MACAU => 'Macau',
            self::MONGOLIA => 'Mongolia',
            self::NORTH_KOREA => 'North Korea',
        };
    }

    public function flag(): string
    {
        return 'flag-' . strtolower($this->value);
    }

    public function currency(): string
    {
        return match ($this) {
            self::UNITED_STATES => 'USD',
            self::CANADA => 'CAD',
            self::UNITED_KINGDOM => 'GBP',
            self::GERMANY, self::FRANCE, self::ITALY, self::SPAIN, self::NETHERLANDS,
            self::BELGIUM, self::SWITZERLAND, self::AUSTRIA, self::SWEDEN, self::NORWAY,
            self::DENMARK, self::FINLAND, self::POLAND, self::CZECH_REPUBLIC, self::HUNGARY,
            self::ROMANIA, self::BULGARIA, self::GREECE, self::PORTUGAL, self::IRELAND,
            self::LUXEMBOURG, self::MALTA, self::CYPRUS, self::ESTONIA, self::LATVIA,
            self::LITHUANIA, self::SLOVENIA, self::SLOVAKIA, self::CROATIA => 'EUR',
            self::AUSTRALIA => 'AUD',
            self::NEW_ZEALAND => 'NZD',
            self::JAPAN => 'JPY',
            self::SOUTH_KOREA => 'KRW',
            self::CHINA => 'CNY',
            self::INDIA => 'INR',
            self::BRAZIL => 'BRL',
            self::MEXICO => 'MXN',
            self::ARGENTINA => 'ARS',
            self::CHILE => 'CLP',
            self::COLOMBIA => 'COP',
            self::PERU => 'PEN',
            self::VENEZUELA => 'VES',
            self::URUGUAY => 'UYU',
            self::PARAGUAY => 'PYG',
            self::BOLIVIA => 'BOB',
            self::ECUADOR => 'USD',
            self::GUYANA => 'GYD',
            self::SURINAME => 'SRD',
            self::SOUTH_AFRICA => 'ZAR',
            self::NIGERIA => 'NGN',
            self::KENYA => 'KES',
            self::GHANA => 'GHS',
            self::ETHIOPIA => 'ETB',
            self::TANZANIA => 'TZS',
            self::UGANDA => 'UGX',
            self::MOROCCO => 'MAD',
            self::ALGERIA => 'DZD',
            self::TUNISIA => 'TND',
            self::EGYPT => 'EGP',
            self::ISRAEL => 'ILS',
            self::TURKEY => 'TRY',
            self::IRAN => 'IRR',
            self::IRAQ => 'IQD',
            self::SAUDI_ARABIA => 'SAR',
            self::UAE => 'AED',
            self::QATAR => 'QAR',
            self::KUWAIT => 'KWD',
            self::BAHRAIN => 'BHD',
            self::OMAN => 'OMR',
            self::YEMEN => 'YER',
            self::JORDAN => 'JOD',
            self::LEBANON => 'LBP',
            self::SYRIA => 'SYP',
            self::RUSSIA => 'RUB',
            self::UKRAINE => 'UAH',
            self::BELARUS => 'BYN',
            self::KAZAKHSTAN => 'KZT',
            self::UZBEKISTAN => 'UZS',
            self::TURKMENISTAN => 'TMT',
            self::KYRGYZSTAN => 'KGS',
            self::TAJIKISTAN => 'TJS',
            self::AFGHANISTAN => 'AFN',
            self::PAKISTAN => 'PKR',
            self::BANGLADESH => 'BDT',
            self::SRI_LANKA => 'LKR',
            self::NEPAL => 'NPR',
            self::BHUTAN => 'BTN',
            self::MYANMAR => 'MMK',
            self::THAILAND => 'THB',
            self::VIETNAM => 'VND',
            self::LAOS => 'LAK',
            self::CAMBODIA => 'KHR',
            self::MALAYSIA => 'MYR',
            self::SINGAPORE => 'SGD',
            self::INDONESIA => 'IDR',
            self::PHILIPPINES => 'PHP',
            self::TAIWAN => 'TWD',
            self::HONG_KONG => 'HKD',
            self::MACAU => 'MOP',
            self::MONGOLIA => 'MNT',
            self::NORTH_KOREA => 'KPW',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->name(), self::cases())
        );
    }
}
