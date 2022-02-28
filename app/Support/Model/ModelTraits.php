<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

trait ModelTraits
{
    use HasFactory;
    use ListHelpers;
    use SerializeDate;
    use SanitizeHelpers;
    use InstanceHelpers;
}
