<?php

namespace Modules\Common\Traits;

use \Exception;
use Carbon\Carbon;
use Throwable;

trait PinTrait
{
    private static $EGN_LENGTH = 10;
    private static $GENDER_MAN = 2;
    private static $GENDER_WOMAN = 1;
    private static $MONTHS_BG = [
        1 => "януари",
        2 => "февруари",
        3 => "март",
        4 => "април",
        5 => "май",
        6 => "юни",
        7 => "юли",
        8 => "август",
        9 => "септември",
        10 => "октомври",
        11 => "ноември",
        12 => "декември"
    ];
    private static $EGN_WEIGHTS = [
        2,
        4,
        8,
        5,
        10,
        9,
        7,
        3,
        6,
    ];
    private static $EGN_REGIONS = [
        "Благоевград" => 43,  /* от 000 до 043 */
        "Бургас" => 93,  /* от 044 до 093 */
        "Варна" => 139, /* от 094 до 139 */
        "Велико Търново" => 169, /* от 140 до 169 */
        "Видин" => 183, /* от 170 до 183 */
        "Враца" => 217, /* от 184 до 217 */
        "Габрово" => 233, /* от 218 до 233 */
        "Кърджали" => 281, /* от 234 до 281 */
        "Кюстендил" => 301, /* от 282 до 301 */
        "Ловеч" => 319, /* от 302 до 319 */
        "Монтана" => 341, /* от 320 до 341 */
        "Пазарджик" => 377, /* от 342 до 377 */
        "Перник" => 395, /* от 378 до 395 */
        "Плевен" => 435, /* от 396 до 435 */
        "Пловдив" => 501, /* от 436 до 501 */
        "Разград" => 527, /* от 502 до 527 */
        "Русе" => 555, /* от 528 до 555 */
        "Силистра" => 575, /* от 556 до 575 */
        "Сливен" => 601, /* от 576 до 601 */
        "Смолян" => 623, /* от 602 до 623 */
        "София - град" => 721, /* от 624 до 721 */
        "София - окръг" => 751, /* от 722 до 751 */
        "Стара Загора" => 789, /* от 752 до 789 */
        "Добрич (Толбухин)" => 821, /* от 790 до 821 */
        "Търговище" => 843, /* от 822 до 843 */
        "Хасково" => 871, /* от 844 до 871 */
        "Шумен" => 903, /* от 872 до 903 */
        "Ямбол" => 925, /* от 904 до 925 */
        "Друг/Неизвестен" => 999, /* от 926 до 999 - Такъв регион понякога се
            ползва при родени преди 1900, за родени в чужбина или ако в даден
            регион се родят повече деца от предвиденото. Доколкото ми е
            известно няма правило при ползването на 926 - 999 */
    ];

    /**
     * [getAgeAndSex description]
     * @param  string $pin
     * @return int|empty string
     */
    public function getAgeAndSex(string $pin)
    {
        try {
            $pinInfo = $this->parse($pin);
            $age = Carbon::createFromDate(
                $pinInfo['year'],
                $pinInfo['month'],
                $pinInfo['day']
            )->age;
            $region = $pinInfo['region_text'];
            $sexIndex = $pinInfo['sex_index'];
        } catch (Throwable  $e) {
            return [];
        }

        return [
            'age' => $age,
            'sex' => $sexIndex,
            'region' => $region,
        ];
    }

    /**
     * @param $egn
     *
     * @return array
     * @throws \Exception
     */
    public function parse(string $pin): array
    {
        if (!$this->isValidPin($pin)) {
            return [];
        }

        $ret = [];
        $ret["year"] = substr($pin, 0, 2);
        $ret["month"] = substr($pin, 2, 2);
        $ret["day"] = substr($pin, 4, 2);
        if ($ret["month"] > 40) {
            $ret["month"] -= 40;
            $ret["year"] += 2000;
        } else {
            if ($ret["month"] > 20) {
                $ret["month"] -= 20;
                $ret["year"] += 1800;
            } else {
                $ret["year"] += 1900;
            }
        }

        $ret["birthday_text"] = (int) $ret["day"] . " "
            . self::$MONTHS_BG[(int)$ret["month"]] . " "
            . $ret["year"] . " г.";

        $region = substr($pin, 6, 3);
        $ret["region_num"] = $region;
        $ret["sex"] = substr($pin, 8, 1) % 2;
        $ret["sex_text"] = "жена";
        if (!$ret["sex"]) {
            $ret["sex_text"] = "мъж";
        }

        if (!$ret["sex"]) {
            $ret["sex_index"] = self::$GENDER_MAN;
        } else {
            $ret["sex_index"] = self::$GENDER_WOMAN;
        }

        $firstRegionNum = 0;
        foreach (self::$EGN_REGIONS as $regionName => $lastRegionNum) {
            if ($region >= $firstRegionNum && $region <= $lastRegionNum) {
                $ret["region_text"] = $regionName;
                break;
            }

            $firstRegionNum = $lastRegionNum + 1;
        }

        if (substr($pin, 8, 1) % 2 != 0) {
            $region--;
        }

        $ret["birthnumber"] = ($region - $firstRegionNum) / 2 + 1;

        return $ret;
    }

    /**
     * [isValidPin description]
     * @param  string  $pin
     * @return bool
     */
    public function isValidPin(?string $pin): bool
    {
        if (strlen($pin) != self::$EGN_LENGTH) {
            return false;
        }

        $year = substr($pin, 0, 2);
        $mon = substr($pin, 2, 2);
        $day = substr($pin, 4, 2);

        if ($mon > 40) {
            if (!checkdate($mon - 40, $day, $year + 2000)) {
                return false;
            }
        } else {
            if ($mon > 20) {
                if (!checkdate($mon - 20, $day, $year + 1800)) {
                    return false;
                }
            } else {
                if (!checkdate($mon, $day, $year + 1900)) {
                    return false;
                }
            }
        }

        $checksum = substr($pin, 9, 1);

        $egnsum = 0;
        for ($i = 0; $i < 9; $i++) {
            $egnsum += substr($pin, $i, 1) * self::$EGN_WEIGHTS[$i];
        }

        $validChecksum = $egnsum % 11;
        if ($validChecksum == self::$EGN_LENGTH) {
            $validChecksum = 0;
        }

        if ($checksum == $validChecksum) {
            return true;
        }

        return false;
    }
}
