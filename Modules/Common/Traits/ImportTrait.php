<?php

namespace Modules\Common\Traits;

use Modules\Common\Entities\Loan;

trait ImportTrait
{
    private static $PAYDAY_ONLINE = 1;
    private static $PAYDAY_OFFICE = 2;
    private static $INSTALLMENTS_ONLINE = 3;
    private static $INSTALLMENTS_OFFICE = 4;

    public function getDocumentTemplateId(
        string $loanType,
        int $fromOffice = 1
    ): ?int {

        if (Loan::TYPE_PAYDAY) {
            if (0 == $fromOffice) {
                return self::$PAYDAY_ONLINE;
            }

            return self::$PAYDAY_OFFICE;
        }

        if (Loan::TYPE_INSTALLMENTS) {
            if (0 == $fromOffice) {
                return self::$INSTALLMENTS_ONLINE;
            }

            return self::$INSTALLMENTS_OFFICE;
        }

        return null;
    }

    public function getPaymentStatusByOverdue(int $overdueDays): string
    {
        if ($overdueDays < 1) {
            return Loan::PAY_STATUS_CURRENT;
        }

        if ($overdueDays >= 1 && $overdueDays <=15) {
            return Loan::PAY_STATUS_1_15;
        }

        if ($overdueDays >= 16 && $overdueDays <=30) {
            return Loan::PAY_STATUS_16_30;
        }

        if ($overdueDays >= 31 && $overdueDays <=60) {
            return Loan::PAY_STATUS_31_60;
        }

        return Loan::PAY_STATUS_LATE;
    }
}
