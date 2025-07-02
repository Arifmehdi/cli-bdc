<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Invoice extends Model
{
    use HasFactory,SoftDeletes;

    public function lead()
    {
    return $this->belongsTo(Lead::class,'lead_id');
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'invoice_inventory');
    }

    public function mainInventories()
    {
        return $this->belongsToMany(MainInventory::class, 'invoice_main_inventory', 'invoice_id', 'main_inventory_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class,'package','id');
    }
    public function dealer()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function get_invoice_id()
    {
        $id= $this->id;
        $mod = $id % 1000000;

        $div_res= (int) ($id / 1000000);

        $prefix="PP";
        switch($div_res){
            case 0:
                $prefix='P';
            break;
            case 1:
                $prefix='B';
            break;
            case 2:
                $prefix='C';
            break;
            case 3:
                $prefix='D';
            break;
            case 4:
                $prefix='E';
            break;
            case 5:
                $prefix='F';
            break;
            case 6:
                $prefix='G';
            break;
            case 7:
                $prefix='H';
            break;
            case 8:
                $prefix='I';
            break;
            case 9:
                $prefix='J';
            break;
            case 10:
                $prefix='K';
            break;
            case 11:
                $prefix='L';
            break;
            case 12:
                $prefix='M';
            break;
            case 13:
                $prefix='N';
            break;
            case 14:
                $prefix='O';
            break;
            case 15:
                $prefix='P';
            break;
            case 16:
                $prefix='Q';
            break;
            case 17:
                $prefix='R';
            break;
            case 18:
                $prefix='S';
            break;
            case 19:
                $prefix='T';
            break;
            case 20:
                $prefix='U';
            break;
            case 21:
                $prefix='V';
            break;
            case 22:
                $prefix='W';
            break;
            case 23:
                $prefix='X';
            break;
            case 24:
                $prefix='Y';
            break;
            case 25:
                $prefix='Z';
            break;
        }
        $num= sprintf("%06d", $mod);
        $generated_id =  $prefix.$num;
        return $generated_id;
    }




}
