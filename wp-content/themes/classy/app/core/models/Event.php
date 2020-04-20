<?php

namespace Classy\Models;

use Helpers\General;

class Event extends BasisPost
{
    public function isUpcoming()
    {
        return strtotime($this->getAcfByKey('acf_date')) >= strtotime(date('Ymd')); // Compare with today
    }
}
