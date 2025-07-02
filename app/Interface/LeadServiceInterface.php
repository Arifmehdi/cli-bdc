<?php

namespace App\Interface;


interface LeadServiceInterface extends BaseServiceInterface
{
    public function getItemByFilter($request);
    public function bulkInvoice(array $ids,$dealer);
    public function getLeadByFilter($request);
    public function getInvoiceByFilter($request);
    public function getUserItemByFilter(int $id,$request);
    public function getUserRowCount(int $id);
    public function getUserTrashedCount(int $id);
}
