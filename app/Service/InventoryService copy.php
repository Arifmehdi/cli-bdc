<?php

namespace App\Service;

use App\Models\Inventory;
use App\Models\LocationCity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Interface\InventoryServiceInterface;


class InventoryService implements InventoryServiceInterface
{

    protected $priceRange;

    public function __construct()
    {
        $this->priceRange = app('priceRange');
    }



    // public function __construct(
    //     private FileUploaderServiceInterface $uploader,
    // ) {
    // }

    public function all()
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        // $leads = Inventory::with(['tmp_inventories_car', 'user'])->orderBy('id', 'desc')->get();
        $inventory = Inventory::query();
        return $inventory;
    }

    public function store(array $visits)
    {
        // abort_if(! auth()->user()->can('hrm_visit_create'), 403, 'Access Forbidden');
        // if (isset($visits['attachments'])) {
        //     $visits['attachments'] = $this->uploader->upload($visits['attachments'], 'uploads/visits/');
        // }
        // $item = Tmp_inventory::create($visits);

        // return $item;
    }

    public function getByUserId(int $userId)
    {
        $item = Inventory::where('deal_id',$userId)->get();
        return $item;
    }

    public function update(array $visit, int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_update'), 403, 'Access Forbidden');
        // $item = Tmp_inventory::find($id);
        // if (isset($visit['attachments'])) {
        //     if (isset($visit['attachments']) && ! empty($visit['attachments']) && file_exists('uploads/visits/'.$visit['old_photo']) && $visit['old_photo'] != null) {
        //         unlink(public_path('uploads/visits/'.$visit['old_photo']));
        //     }
        //     $visit['attachments'] = $this->uploader->upload($visit['attachments'], 'uploads/visits/');
        // } else {
        //     // unlink(public_path('uploads/visits/'.$visit['old_photo']));
        //     $visit['attachments'] = null;
        // }
        // $updatedItem = $item->update($visit);

        // return $updatedItem;
    }

    public function find(int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_view'), 403, 'Access Forbidden');
        // $item = Tmp_inventory::find($id);
        $item = Inventory::find($id);

        return $item;
    }

    //Move To Trash
    public function trash($data)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        // $item = Inventory::find($id);
        // $item->delete($item);

        $inventory = Inventory::find($data->id);
        $inventory->delete();
        return $inventory;
    }
    //Bulk Move To Trash
    public function bulkTrash(array $ids)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        foreach ($ids as $id) {
            $item = Inventory::find($id);
            $item->delete($item);
        }

        return $item;
    }

    //Get Trashed Item list
    public function getTrashedItem($id=null)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        if($id != null)
        {
            $item = Inventory::where('deal_id',$id)->onlyTrashed()->orderBy('id', 'desc')->get();
        }else
        {

            $item = Inventory::onlyTrashed()->orderBy('id', 'desc')->get();
        }
        return $item;
    }

    //Permanent Delete
    public function permanentDelete(int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        $item = Inventory::onlyTrashed()->find($id);
        $item->forceDelete();
        return $item;
    }


    //Bulk Permanent Delete
    public function bulkPermanentDelete(array $ids)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        foreach ($ids as $id) {
            $item = Inventory::onlyTrashed()->find($id);
            $item->forceDelete($item);
        }

        return $item;
    }


    //Bulk Active function

    public function bulkActive(array $ids)
    {
        foreach ($ids as $id) {
            $inventory = Inventory::find($id);
            $inventory->is_visibility = '1';
            $inventory->save();

        }
    }

    //Bulk Inactive function

    public function bulkInactive(array $ids)
    {
        foreach ($ids as $id) {
            $inventory = Inventory::find($id);
            $inventory->is_visibility = '0';
            $inventory->save();

        }
    }

    //Restore Trashed Item
    public function restore(int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        $item = Inventory::withTrashed()->find($id)->restore();
        return $item;
    }


    //Bulk Restore Trashed Item
    public function bulkRestore(array $ids)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        foreach ($ids as $id) {
            $item = Inventory::withTrashed()->find($id);
            $item->restore($item);
        }

        return $item;
    }


    //Get Row Count
    public function getRowCount($id =null)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        if($id != null)
        {
            $count = Inventory::where('deal_id',$id)->count();
        }else
        {
            $count = Inventory::all()->count();
        }

        return $count;
    }

    //Get Row Count
    public function getUserByRowCount($id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        $count = Inventory::where('deal_id',$id)->count();
        return $count;
    }

    //Get Trashed Item Count
    public function getTrashedCount($id=null)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        if($id != null)
        {
            $count = Inventory::where('deal_id',$id)->onlyTrashed()->count();
        }else
        {
            $count = Inventory::onlyTrashed()->count();
        }

        return $count;
    }

    //Get Trashed Item Count
    public function getUserByTrashedCount($id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        $count = Inventory::where('deal_id',$id)->onlyTrashed()->count();
        return $count;
    }


    public function getItemByFilter($request,$id=null)
    {


         dd('id =',$id,$request->all());
        $requestURL = $request->requestURL;
        $urlComponents = parse_url($requestURL);
        $queryString = $urlComponents['query'] ?? '';
        parse_str($queryString, $queryParams);
        $lowestValue = $queryParams['lowestPrice'] ?? null;
        $bestDealValue = $queryParams['bestDeal'] ?? null;
        $lowestMileageValue = $queryParams['lowestMileage'] ?? null;
        $ownedValue = $queryParams['owned'] ?? null;
        $makeTypeSearchValue = $queryParams['makeTypeSearch'] ?? null;
        $homeBodySearch = $queryParams['body'] ?? null;
        $homepage = $queryParams['home'] ?? null;
        $hometypeSearch = $queryParams['homeBodySearch'] ?? null;
        $homeMakeSearch = $queryParams['make'] ?? null;
        $homeModelSearch = $queryParams['model'] ?? null;
        $homePriceSearch = $queryParams['maximum_price'] ?? null;
        $homeDealerCitySearch = $queryParams['homeDealerCitySearch'] ?? null;
        $homeDealerStateSearch = $queryParams['homeDealerStateSearch'] ?? null;
        $homeLocationSearch = $queryParams['zip'] ?? null;
        $homeRadiusSearch = $queryParams['radius'] ?? null;
        // $homeLocationSearch2 = $queryParams['homeLocationSearch2'] ?? null;
        $homeMileageSearch = $queryParams['maximum_miles'] ?? null;
        $homeMinMileageSearch = $queryParams['min-miles'] ?? null;
        $homeMaxMileageSearch = $queryParams['max-miles'] ?? null;

        $homeMinPayment = $queryParams['min_payment'] ?? null;
        $homeMaxPayment = $queryParams['max_payment'] ?? null;
        $homeMinYear = $queryParams['min_year'] ?? null;
        $homeMaxYear = $queryParams['max_year'] ?? null;
        $minPriceBody = $queryParams['min_price'] ?? null;
        $maxPriceBody = $queryParams['max_price'] ?? null;

        $zipCode  = $homeLocationSearch;
        $countryCode = 'US';

        //dd($homepage);

        //****************** */ saved local storage all search data **********************************************************

        if($hometypeSearch == 'new')
        {
// dd($request->all());
            $searchData = [
                'newfirstzipFilter' => $request->firstzipFilter,
                'newfirstMakeFilter' => $request->firstMakeFilter,
                'newfirstModelFilter' => $request->firstModelFilter,
                'newweb_search_any' => $request->web_search_any,
                'newmakeCheckdata' => $request->makeCheckdata,
                'newautoMaxBodyCheckbox' => $request->autoMaxBodyCheckbox,
                'newautoMinYearCheckbox' => $request->autoMinYearCheckbox,
                'newautoMaxYearCheckbox' => $request->autoMaxYearCheckbox,
                'newrangerMinPriceSlider' => $request->rangerMinPriceSlider,
                'newrangerMaxPriceSlider' => $request->rangerMaxPriceSlider,
                'newrangerMileageMinPriceSlider' => $request->rangerMileageMinPriceSlider,
                'newrangerMileageMaxPriceSlider' => $request->rangerMileageMaxPriceSlider,
                'newrangerYearMinPriceSlider' => $request->rangerYearMinPriceSlider,
                'newrangerYearMaxPriceSlider' => $request->rangerYearMaxPriceSlider,
                'newtotalLoanAmountCalculation' => $request->totalLoanAmountCalculation,
                'newautoWebTransmissionCheckbox' => $request->autoWebTransmissionCheckbox,
                'newautoWebFuelCheckbox' => $request->autoWebFuelCheckbox,
                'newautoWebDriveTrainCheckbox' => $request->autoWebDriveTrainCheckbox ??  $request->autoMobileDriveTrainCheckbox,
                'newwebColorFilter' => $request->webColorFilter,
                'newwebBodyFilter' => $homeBodySearch,
                // mobile version filter data
                'newmobileRangerMinPriceSlider' => $request->mobileRangerMinPriceSlider,
                'newmobileRangerMaxPriceSlider' => $request->mobileRangerMaxPriceSlider,
                'newmobileMileageRangerMinPriceSlider' => $request->mobileMileageRangerMinPriceSlider,
                'newmobileMileageRangerMaxPriceSlider' => $request->mobileMileageRangerMaxPriceSlider,
                'newmobileYearRangerMinPriceSlider' => $request->mobileYearRangerMinPriceSlider,
                'newmobileYearRangerMaxPriceSlider' => $request->mobileYearRangerMaxPriceSlider,
                'newautoMobileTypeCheckbox' => $request->autoMobileTypeCheckbox,
                'newsecondFilterMakeInputNew' => $request->secondFilterMakeInputNew,
                'newsecondFilterModelInputNew' => $request->secondFilterModelInputNew,
                'newautoMobileFuelCheckbox' => $request->autoMobileFuelCheckbox,
                'newautoMobileTransmissionCheckbox' => $request->autoMobileTransmissionCheckbox,
                'newmobileBody' => $homeBodySearch,
                'newmobileColorFilter' => $request->mobileColorFilter,
            ];



            Cookie::queue('searchData',json_encode($searchData), 120);
        }else
        {

            $searchData = [
                'firstzipFilter' => $request->firstzipFilter,
                'firstMakeFilter' => $request->firstMakeFilter,
                'firstModelFilter' => $request->firstModelFilter,
                'web_search_any' => $request->web_search_any,
                'makeCheckdata' => $request->makeCheckdata,
                'autoMaxBodyCheckbox' => $request->autoMaxBodyCheckbox,
                'autoMinYearCheckbox' => $request->autoMinYearCheckbox,
                'autoMaxYearCheckbox' => $request->autoMaxYearCheckbox,
                'rangerMinPriceSlider' => $request->rangerMinPriceSlider,
                'rangerMaxPriceSlider' => $request->rangerMaxPriceSlider,
                'rangerMileageMinPriceSlider' => $request->rangerMileageMinPriceSlider,
                'rangerMileageMaxPriceSlider' => $request->rangerMileageMaxPriceSlider,
                'rangerYearMinPriceSlider' => $request->rangerYearMinPriceSlider,
                'rangerYearMaxPriceSlider' => $request->rangerYearMaxPriceSlider,
                'totalLoanAmountCalculation' => $request->totalLoanAmountCalculation,
                'autoWebConditionCheckbox' => $request->autoWebConditionCheckbox,
                'autoWebTransmissionCheckbox' => $request->autoWebTransmissionCheckbox,
                'autoWebFuelCheckbox' => $request->autoWebFuelCheckbox,
                'autoWebDriveTrainCheckbox' => $request->autoWebDriveTrainCheckbox ??  $request->autoMobileDriveTrainCheckbox,
                'webColorFilter' => $request->webColorFilter,
                'webMakeFilterMakeInput' => $request->webMakeFilterMakeInput,
                'webBodyFilter'=>  $homeBodySearch,
                'mobileBody' => $homeBodySearch,
                // mobile version filter data
                'mobileRangerMinPriceSlider' => $request->mobileRangerMinPriceSlider,
                'mobileRangerMaxPriceSlider' => $request->mobileRangerMaxPriceSlider,
                'mobileMileageRangerMinPriceSlider' => $request->mobileMileageRangerMinPriceSlider,
                'mobileMileageRangerMaxPriceSlider' => $request->mobileMileageRangerMaxPriceSlider,
                'mobileYearRangerMinPriceSlider' => $request->mobileYearRangerMinPriceSlider,
                'mobileYearRangerMaxPriceSlider' => $request->mobileYearRangerMaxPriceSlider,
                'autoMobileTypeCheckbox' => $request->autoMobileTypeCheckbox,
                'secondFilterMakeInputNew' => $request->secondFilterMakeInputNew,
                'secondFilterModelInputNew' => $request->secondFilterModelInputNew,
                'autoMobileFuelCheckbox' => $request->autoMobileFuelCheckbox,
                'autoMobileTransmissionCheckbox' => $request->autoMobileTransmissionCheckbox,
                'mobileColorFilter' => $request->mobileColorFilter,
            ];

            Cookie::queue('searchData',json_encode($searchData), 120);
        }



        $query = Inventory::with('dealer');

//  query end here //  query end here //  query end here //  query end here //  query end here //  query end here //  query end here

        if ($request->web_search_any) {
            $searchWords = explode(' ', $request->web_search_any);

            $query->where(function ($subquery) use ($searchWords) {
                $subquery->where(function ($subquery2) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $subquery2->where(function ($subquery3) use ($word) {
                            $subquery3->where('make', 'like', '%' . $word . '%')
                                ->orWhere('model', 'like', '%' . $word . '%')
                                ->orWhere('stock', 'like', '%' . $word . '%')
                                ->orWhere('year', 'like', '%' . $word . '%')
                                ->orWhere('zip_code', 'like', '%' . $word . '%')
                                ->orWhere('vin', 'like', '%' . $word . '%');
                                // ->orWhere('body_formated ', 'like', '%' . $word . '%');
                        });
                    }
                })
                ->orWhere(function ($subquery4) use ($searchWords) {
                    $subquery4->whereRaw("CONCAT_WS('', year, make, model,vin ) LIKE ?", ['%' . implode('%', $searchWords) . '%']);
                });
            });
        }

        $sortMapping = [
            'datecreated|desc' => ['stock_date_formated', 'desc'],
            'datecreated|asc' => ['stock_date_formated', 'asc'],
            'searchprice|asc' => ['price', 'asc'],
            'searchprice|desc' => ['price', 'desc'],
            'mileage|asc' => ['miles', 'asc'],
            'mileage|desc' => ['miles', 'desc'],
            'modelyear|asc' => ['year', 'asc'],
            'modelyear|desc' => ['year', 'desc'],
            'payment|asc' => ['payment_price', 'asc'],
            'payment|desc' => ['payment_price', 'desc']
        ];



        //Cookie::queue('selected_sort_search',$request->selected_sort_search, 120);
        Session::put('selected_sort_search',$request->selected_sort_search);

        if (isset($sortMapping[$request->selected_sort_search])) {
            $query->orderBy($sortMapping[$request->selected_sort_search][0], $sortMapping[$request->selected_sort_search][1]);
        }

        if ($request->mobile_web_search_any) {
            $searchWords = explode(' ', $request->mobile_web_search_any);

            $query->where(function ($subquery) use ($searchWords) {
                $subquery->where(function ($subquery2) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $subquery2->where(function ($subquery3) use ($word) {
                            $subquery3->where('make', 'like', '%' . $word . '%')
                                ->orWhere('model', 'like', '%' . $word . '%')
                                ->orWhere('stock', 'like', '%' . $word . '%')
                                ->orWhere('year', 'like', '%' . $word . '%')
                                ->orWhere('zip_code', 'like', '%' . $word . '%')
                                ->orWhere('vin', 'like', '%' . $word . '%');
                                // ->orWhere('body_formated ', 'like', '%' . $word . '%');
                        });
                    }
                })
                ->orWhere(function ($subquery4) use ($searchWords) {
                    $subquery4->whereRaw("CONCAT_WS('', year, make, model,vin ) LIKE ?", ['%' . implode('%', $searchWords) . '%']);
                });
            });
        }


        // may be no need
        if ($homeDealerCitySearch != null) {
            $query->whereHas('dealer', function ($q) use ($homeDealerCitySearch) {
                $q->where('city', 'like', '%' . $homeDealerCitySearch . '%');
            });
        }

        if ($lowestValue == null && $lowestMileageValue == null && $ownedValue == null) {

            if ($makeTypeSearchValue != null) {
                $query->where('make', $makeTypeSearchValue);
            }

            if ($homeMakeSearch != null) {
                $query->where('make', $homeMakeSearch);
            }
            if ($homeModelSearch != null) {
                $query->where('model', $homeModelSearch);
            }


            if ($homePriceSearch != null) {
                switch ($homePriceSearch) {
                    case "0":
                        $query->where('price', '<=', 5000);
                        break;

                    case "1":
                        $query->where('price', '<=', 10000);
                        // $query->whereBetween('price', [5000, 10000]);
                        break;

                    case "2":
                        $query->where('price', '<=', 20000);
                        break;

                    case "3":
                        $query->where('price', '<=', 30000);
                        break;

                    case "4":
                        $query->where('price', '<=', 40000);
                        break;

                    case "5":
                        $query->where('price', '<=', 50000);
                        break;

                    case "6":
                        $query->where('price', '<=', 60000);
                        break;

                    case "7":
                        $query->where('price', '<=', 70000);
                        break;
                    case "8":
                        $query->where('price', '<=', 80000);
                        break;

                    default:
                        $query->where('price', '<=', 100000);
                        break;
                }

            }

            if ($homeMileageSearch != null) {
                $query->where('miles','<=', $homeMileageSearch);
            }

            // if ($minPriceBody != null || $maxPriceBody != null) {
            //     $minValue = ($minPriceBody != null) ? $minPriceBody : 0;
            //     $maxValue = ($maxPriceBody != null) ? $maxPriceBody : 1000000;
            //     $query->whereBetween('price', [$minValue, $maxValue]);
            // }

            if ($minPriceBody  || $maxPriceBody ) {
                $minValue = ($minPriceBody !== null) ? $minPriceBody : 0;
                $maxValue = ($maxPriceBody !== null) ? $maxPriceBody : 150000;

                // If the max value is 150000, it means we need to show all values
                if ($maxValue == 150000) {
                    // $minPrice_provide = $this->priceRange['used']['minPrice'];
                    // $maxPrice_provide = $this->priceRange['used']['maxPrice'];
                    // // $query->where('price', '>=', $minValue);
                    // $query->whereBetween('price', [$minPrice_provide, $maxPrice_provide]);
                } else {
                    $query->whereBetween('price', [$minValue, $maxValue]);
                }
            }

            if ($homeMinMileageSearch || $homeMaxMileageSearch ) {
                $minMileage = ($homeMinMileageSearch !== null) ? $homeMinMileageSearch : 0;
                $maxMileage = ($homeMaxMileageSearch !== null) ? $homeMaxMileageSearch : 150000;

                if ($maxMileage == 150000) {
                    // // $query->where('miles', '>=', $minMileage);
                    // // $minPrice = $this->priceRange['new']['minMiles'];
                    // $minMiles_provide = $this->priceRange['used']['minMiles'];
                    // $maxMiles_provide = $this->priceRange['used']['maxMiles'];

                    // $query->whereBetween('miles', [$minMiles_provide, $maxMiles_provide]);

                } else {
                    $query->whereBetween('miles', [$minMileage, $maxMileage]);
                }
            }



            if (($request->rangerMinPriceSlider != null || $request->rangerMaxPriceSlider != null)) {
                $minValue = ($request->rangerMinPriceSlider != null) ? $request->rangerMinPriceSlider : 0;
                $maxValue = ($request->rangerMaxPriceSlider != null) ? $request->rangerMaxPriceSlider : 1000010;

                if ($minValue > 10001) {
                    $query->whereNotNull('price');

                } else {
                    $query->whereBetween('price', [$minValue, $maxValue]);
                }
            }

            if ($request->rangerMileageMinPriceSlider != null || $request->rangerMileageMaxPriceSlider != null) {
                $minValue = ($request->rangerMileageMinPriceSlider != null) ? $request->rangerMileageMinPriceSlider : 0;
                $maxValue = ($request->rangerMileageMaxPriceSlider != null) ? $request->rangerMileageMaxPriceSlider : 150000;

                $query->whereBetween('miles', [$minValue, $maxValue]);
            }

            if ($request->rangerYearMinPriceSlider != null || $request->rangerYearMaxPriceSlider != null) {
                $minValue = ($request->rangerYearMinPriceSlider != null) ? $request->rangerYearMinPriceSlider : 1985;
                $maxValue = ($request->rangerYearMaxPriceSlider != null) ? $request->rangerYearMaxPriceSlider : 2024;
                $query->whereBetween('year', [$minValue, $maxValue]);
            }

            if ($request->mobileRangerMinPriceSlider != null || $request->mobileRangerMaxPriceSlider != null) {
                $minValue = ($request->mobileRangerMinPriceSlider != null) ? $request->mobileRangerMinPriceSlider : 0;
                $maxValue = ($request->mobileRangerMaxPriceSlider != null) ? $request->mobileRangerMaxPriceSlider : 1000000;

                $query->whereBetween('price', [$minValue, $maxValue]);
            }

            if ($request->mobileMileageRangerMinPriceSlider != null || $request->mobileMileageRangerMaxPriceSlider != null) {
                $minValue = ($request->mobileMileageRangerMinPriceSlider != null) ? $request->mobileMileageRangerMinPriceSlider : 0;
                $maxValue = ($request->mobileMileageRangerMaxPriceSlider != null) ? $request->mobileMileageRangerMaxPriceSlider : 1000000;

                $query->whereBetween('miles', [$minValue, $maxValue]);
            }

            if ($request->mobileYearRangerMinPriceSlider != null || $request->mobileYearRangerMaxPriceSlider != null) {
                $minValue = ($request->mobileYearRangerMinPriceSlider != null) ? $request->mobileYearRangerMinPriceSlider : 1985;
                $maxValue = ($request->mobileYearRangerMaxPriceSlider != null) ? $request->mobileYearRangerMaxPriceSlider : 2024;

                $query->whereBetween('year', [$minValue, $maxValue]);
            }
            if ($homeMinPayment != null || $homeMaxPayment != null) {
                $minPaymentValue = ($homeMinPayment != null) ? $homeMinPayment : 0;
                $maxPaymentValue = ($homeMaxPayment != null) ? $homeMaxPayment : 5000;

                $query->whereBetween('payment_price', [$minPaymentValue, $maxPaymentValue]);
            }

            if ($homeMinYear != null || $homeMaxYear != null) {
                $minYearValue = ($homeMinYear != null) ? $homeMinYear : 1985;
                $maxYearValue = ($homeMaxYear != null) ? $homeMaxYear : date('yyyy');

                $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            }

            if ($request->autoMinYearCheckbox != null || $request->autoMaxYearCheckbox != null) {
                $minYearValue = ($request->autoMinYearCheckbox != null) ? $request->autoMinYearCheckbox : 1985;
                $maxYearValue = ($request->autoMaxYearCheckbox != null) ? $request->autoMaxYearCheckbox : date('yyyy');
                $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            }

            if ($request->autoMobileMinYearCheckbox != null || $request->autoMobileMaxYearCheckbox != null) {
                $minYearValue = ($request->autoMobileMinYearCheckbox != null) ? $request->autoMobileMinYearCheckbox : 1985;
                $maxYearValue = ($request->autoMobileMaxYearCheckbox != null) ? $request->autoMobileMaxYearCheckbox : date('yyyy');
                $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            }

            if ($request->firstzipFilter != null) {
                $query->where('zip_code', $request->firstzipFilter);
            }



            if ($request->webCity != null) {
                $query->whereHas('dealer', function ($q) use ($request) {
                    $q->where('city', $request->webCity);
                });
            }



            if ($request->firstMakeFilter != null) {
                $query->where('make', $request->firstMakeFilter);
            }
            if ($request->firstModelFilter != null) {
                $query->where('model', $request->firstModelFilter);
            }
            if ($request->makeCheckdata != null) {
                $query->whereIn('make', $request->makeCheckdata);
            }

            if ($request->has('autoMobileTypeCheckbox')) {
                $mobileSelectedTypes = $request->autoMobileTypeCheckbox;


                // Initialize an empty array to collect the types for filtering
                $types = [];

                if (in_array('Certified', $mobileSelectedTypes)) {
                    $types[] = 'Certified Preowned';
                }
                if (in_array('Preowned', $mobileSelectedTypes)) {
                    $types[] = 'Preowned';
                }
                if (in_array('New', $mobileSelectedTypes)) {
                    $types[] = 'New';
                }

                // Apply the query only if there are selected types
                if (!empty($types)) {
                    $query->whereIn('type', $types);
                }
            }


            if ($request->has('autoWebConditionCheckbox')) {
                $selectedTypes = $request->autoWebConditionCheckbox;

                $types = [];

                if (in_array('Certified', $selectedTypes)) {
                    $types[] = 'Certified Preowned';
                }
                if (in_array('Preowned', $selectedTypes)) {
                    $types[] = 'Preowned';
                }
                if (in_array('New', $selectedTypes)) {
                    $types[] = 'New';
                }

                // Apply the query only if there are selected types
                if (!empty($types)) {
                    $query->whereIn('type', $types);
                }
            }

            if ($request->autoMobileFuelCheckbox != null) {
                if ($request->has('allFuelName') && $request->allFuelName == 'allFuelValue') {
                    // "All" is selected, so no filter is applied
                } else if ($request->has('autoMobileFuelCheckbox')) {
                $query->whereIn('fuel', $request->autoMobileFuelCheckbox);
                }
            }



            if ($request->autoMobileTransmissionCheckbox != null) {
                if ($request->has('allTransmissionlName') && $request->allTransmissionlName == 'allTransmissionValue') {
                    // "All" is selected, so no filter is applied
                } else if ($request->has('autoMobileTransmissionCheckbox')) {
                    $transmissions = $request->autoMobileTransmissionCheckbox;
                    $query->where(function($subQuery) use ($transmissions) {
                        foreach ($transmissions as $transmission) {
                            if (trim($transmission) == 'automatic') {
                                $subQuery->orWhere('transmission', 'LIKE', '%automatic%')
                                         ->orWhere('transmission', 'LIKE', '%variable%');
                            } else {
                                $subQuery->orWhere('transmission', 'LIKE', '%' . trim($transmission) . '%');
                            }
                        }
                    });
                }
            }



            if ($request->autoWebTransmissionCheckbox != null) {
                if ($request->has('allWebTransmissionlName') && $request->allWebTransmissionlName == 'allWebTransmissionValue') {
                } else if ($request->has('autoWebTransmissionCheckbox')) {
                    $Web_transmissions = $request->autoWebTransmissionCheckbox;
                    $query->where(function($subQuery) use ($Web_transmissions) {
                        foreach ($Web_transmissions as $transmission_info) {
                            if (trim($transmission_info) == 'automatic') {
                                $subQuery->orWhere('transmission', 'LIKE', '%automatic%')
                                         ->orWhere('transmission', 'LIKE', '%variable%');
                            } else {
                                $subQuery->orWhere('transmission', 'LIKE', '%' . trim($transmission_info) . '%');
                            }
                        }
                    });
                }
            }

            if ($request->autoWebFuelCheckbox != null) {
                if ($request->has('allWebFuellName') && $request->allWebFuellName == 'allWebFuelValue') {
                } else if ($request->has('autoWebFuelCheckbox')) {
                $query->whereIn('fuel', $request->autoWebFuelCheckbox);
                }
            }

            if ($request->autoWebDriveTrainCheckbox != null) {
                if ($request->has('allWebDriveTrainlName') && $request->allWebFuellName == 'allWebDriveTrainValue') {
                } else if ($request->has('autoWebDriveTrainCheckbox')) {
                $query->whereIn('drive_info', $request->autoWebDriveTrainCheckbox);
                }
            }
            if ($request->autoMobileDriveTrainCheckbox != null) {
                if ($request->has('allMobileDriveTrainlName') && $request->allWebFuellName == 'allMobileDriveTrainValue') {
                } else if ($request->has('autoMobileDriveTrainCheckbox')) {
                $query->whereIn('drive_info', $request->autoMobileDriveTrainCheckbox);
                }
            }


            if ($request->mobileBody != null) {
                $mobile_body = $request->mobileBody;
                $query->Where('body_formated', 'LIKE', '%' . $mobile_body . '%');
            }


            if ($request->webBodyFilter != null) {
                $web_body = $request->webBodyFilter;
                $query->Where('body_formated', 'LIKE', '%' . $web_body . '%');
            }


            if ($request->webColorFilter != null) {
                $web_body = $request->webColorFilter;

                $query->where(function ($subQuery) use ($web_body) {
                    foreach ($web_body as $body) {
                        $subQuery->orWhere('exterior_color', 'LIKE', '%' . $body . '%');
                    }
                });
            }

            if ($request->autoMaxBodyCheckbox != null) {
                if($request->autoMaxBodyCheckbox[0] == null){
                    $query->whereNull('body_formated')->orWhereIn('body_formated', $request->autoMaxBodyCheckbox);
                }else{

                    $query->whereIn('body_formated', $request->autoMaxBodyCheckbox);
                }
            }
            if ($request->autoMobileMakeCheckbox != null) {
                $query->whereIn('make', $request->autoMobileMakeCheckbox);
            }
            if ($request->secondFilterMakeInputNew != null) {
                $query->where('make', $request->secondFilterMakeInputNew);
            }
            if ($request->secondFilterModelInputNew != null) {
                $query->where('model', $request->secondFilterModelInputNew);
            }
            if ($request->autoMobileMaxBodyCheckbox != null) {
                if($request->autoMobileMaxBodyCheckbox[0] == null){
                    $query->whereNull('body_formated')->orWhereIn('body_formated', $request->autoMobileMaxBodyCheckbox);
                }else{

                    $query->whereIn('body_formated', $request->autoMobileMaxBodyCheckbox);
                }
            }

            if ($homeBodySearch != null ) {
                $query->where('body_formated', $homeBodySearch);
            }


            // mobile filter start here
            if ($request->secondFilterZipInput != null) {
                $query->where('zip_code', $request->secondFilterZipInput);
            }
            if ($request->secondFilterMakeInput != null) {
                $query->where('make', $request->secondFilterMakeInput);
            }
            if ($request->secondFilterModelInput != null) {
                $query->where('model', $request->secondFilterModelInput);
            }
            if ($request->webMakeFilterMakeInput != null) {
                Cookie::queue(Cookie::forget('searchData'));
                $searchData = ['webMakeFilterMakeInput' => $request->webMakeFilterMakeInput];
                Cookie::queue('searchData',json_encode($searchData), 120);
                $query->where('make', $request->webMakeFilterMakeInput);
            }
            if ($request->webModelFilterInput != null) {
                $query->where('model', $request->webModelFilterInput);
            }
            if ($request->totalLoanAmountCalculation != null) {
                $format_price  =intVal(str_replace(',','',$request->totalLoanAmountCalculation));
                $query->whereBetween('payment_price', [0, $format_price]);
            }

            if ($request->mobileColorFilter != null) {
                $mobile_color = $request->mobileColorFilter;
                $query->where(function ($subQuery) use ($mobile_color) {
                    foreach ($mobile_color as $mobile_color) {
                        $subQuery->orWhere('exterior_color', 'LIKE', '%' . $mobile_color . '%');
                    }
                });
            }


            // mobile filter end here
        } else {
            if ($lowestValue != null) {
                $query->orderBy('price');
            }
            if ($lowestMileageValue != null) {
                $query->orderBy('miles');
            }
        }



        if($id != null)
        {
            $query->where('deal_id',$id);
        }

        return $query;
    }


    public function getItemByUser($id)
    {

        $inventory = Inventory::where('deal_id',$id)->get();
        return $inventory;
    }


    function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2, $earthRadius = 3959)
{
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;

    $a = sin($dlat / 2) * sin($dlat / 2) +
        cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}


}
