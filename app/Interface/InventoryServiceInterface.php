<?php

namespace App\Interface;


interface InventoryServiceInterface extends BaseServiceInterface
{
    public function getItemByFilterWithOptimized($request,$id = null);
    public function getRowCountOptimized($request, $id = null);
    public function getTrashedCountOptimizedCount($id = null);
    public function getTrashedItemsOptimized($id = null);
    
    public function getItemByFilterOnly($request,$id = null);
    // public function getItemByFilterByCache($cached_data ,$request,$id = null);
    public function getItemByFilter($request,$id = null, $mainInventory);
    public function getItemByUser($id);
    public function getByUserId(int $userId);
    public function getUserByRowCount(int $id);
    public function getUserByTrashedCount(int $id);
    public function bulkInactive(array $ids);
    public function bulkActive(array $ids);
}
