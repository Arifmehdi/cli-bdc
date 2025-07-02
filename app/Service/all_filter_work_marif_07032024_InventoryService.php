<?php

namespace App\Service;

use App\Interface\InventoryServiceInterface;
use App\Models\Inventory;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class InventoryService implements InventoryServiceInterface
{
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
        // dd($request->all());
        $requestURL = $request->requestURL;
        $urlComponents = parse_url($requestURL);
        $queryString = $urlComponents['query'] ?? '';
        parse_str($queryString, $queryParams);
        $lowestValue = $queryParams['lowestPrice'] ?? null;
        $bestDealValue = $queryParams['bestDeal'] ?? null;
        $lowestMileageValue = $queryParams['lowestMileage'] ?? null;
        $ownedValue = $queryParams['owned'] ?? null;
        $makeTypeSearchValue = $queryParams['makeTypeSearch'] ?? null;
        $homeBodySearch = $queryParams['homeBodySearch'] ?? null;
        $homeMakeSearch = $queryParams['homeMakeSearch'] ?? null;
        $homeModelSearch = $queryParams['homeModelSearch'] ?? null;
        $homePriceSearch = $queryParams['homePriceSearch'] ?? null;
        $homeDealerCitySearch = $queryParams['homeDealerCitySearch'] ?? null;
        $homeDealerStateSearch = $queryParams['homeDealerStateSearch'] ?? null;
        $homeLocationSearch = $queryParams['homeLocationSearch'] ?? null;
        // $homeLocationSearch2 = $queryParams['homeLocationSearch2'] ?? null;
        $homeMileageSearch = $queryParams['homeMileageSearch'] ?? null;
        $homeMinPayment = $queryParams['homeMinPayment'] ?? null;
        $homeMaxPayment = $queryParams['homeMaxPayment'] ?? null;
        $homeMinYear = $queryParams['homeMinYear'] ?? null;
        $homeMaxYear = $queryParams['homeMaxYear'] ?? null;
        $minPriceBody = $queryParams['minPriceBodySearch'] ?? null;
        $maxPriceBody = $queryParams['maxPriceBodySearch'] ?? null;
        //****************** */ saved local storage all search data **********************************************************

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
            'autoWebFuelCheckbox' => $request->autoWebFuelCheckbox,
            'webColor' => $request->webcolor,
            // 'mobileBody' => $request->mobileBody,
            // 'mobileColor' => $request->mobileColor,
            'mobileBody' => $request->mobileBody,
        ];
        Cookie::queue('searchData',json_encode($searchData), 120);
        //****************** */End  saved local storage all search data **********************************************************
        // $inventories = TmpInventories::orderByDesc('created_at')->paginate(21);
        // $inventories = Inventory::orderByDesc('id')->paginate(15);
        Session::put('searchData', $searchData);

        $query = Inventory::with('dealer');

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




        // if ($homeDealerCitySearch != null) {
        //     // $query->get()->where('dealer.city','like', '%' . $homeDealerCitySearch . '%');
        //     $query->get()->where('dealer.city', $homeDealerCitySearch );
        // }
        // if ($homeDealerStateSearch != null) {
        //     // $query->get()->where('dealer.state', 'like', '%' . $homeDealerStateSearch . '%');
        //     $query->get()->where('dealer.state',$homeDealerStateSearch );
        //     return $query;
        // }

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
            // if ($homeMinPayment != null) {
            //     $query->where('payment_price', '<=', $homeMinPayment);
            // }
            // if ($homeMaxPayment != null) {
            //     $query->where('payment_price', '>=', $homeMaxPayment);
            // }
            // if ($homeMinYear != null) {
            //     $query->where('year', '<=', $homeMinYear);
            // }
            // if ($homeMaxYear != null) {
            //     $query->where('year', '>=', $homeMaxYear);
            // }

            if ($minPriceBody != null || $maxPriceBody != null) {
                $minValue = ($minPriceBody != null) ? $minPriceBody : 0;
                $maxValue = ($maxPriceBody != null) ? $maxPriceBody : 1000000;

                $query->whereBetween('price', [$minValue, $maxValue]);
            }


            if ($request->rangerMinPriceSlider != null || $request->rangerMaxPriceSlider != null) {
                $minValue = ($request->rangerMinPriceSlider != null) ? $request->rangerMinPriceSlider : 0;
                $maxValue = ($request->rangerMaxPriceSlider != null) ? $request->rangerMaxPriceSlider : 1000000;

                $query->whereBetween('price', [$minValue, $maxValue]);
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
            // if ($homeMinYear != null || $homeMaxYear != null) {
                //     $minYearValue = ($homeMinYear != null) ? $homeMinYear : 1990;
                //     $maxYearValue = ($homeMaxYear != null) ? $homeMaxYear : date('yyyy');

                //     $query->whereBetween('year', [$minYearValue, $maxYearValue]);
                // } autoMinYearCheckbox autoMaxYearCheckbox
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
            if ($request->mobilelocation != null) {
                $query->where('zip_code', $request->mobilelocation);
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
            if ($request->autoMobileTypeCheckbox != null) {
                if ($request->has('checkbox2') && $request->checkbox2 == 'option2') {
                    // dd('all all ');
                    // "All" is selected, so no filter is applied
                } else if ($request->has('autoMobileTypeCheckbox')) {
                    $selectedTypes = $request->autoMobileTypeCheckbox;
                    $type_data = [];

                    if (in_array('certifiedType', $selectedTypes)) {
                        $type_data[] = 'Certified Preowned';
                    }
                    if (in_array('usedType', $selectedTypes)) {
                        $type_data[] = 'Preowned';
                    }

                    if (!empty($type_data)) {
                        $query->whereIn('type', $type_data);
                    }
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
                    // $query->whereIn('transmission', $request->autoMobileTransmissionCheckbox);
                    $transmissions = $request->autoMobileTransmissionCheckbox;
                    $query->where(function($subQuery) use ($transmissions) {
                        foreach ($transmissions as $transmission) {
                            $subQuery->orWhere('transmission', 'LIKE', '%' . $transmission . '%');
                        }
                    });
                }
            }

            // ***** web filter checkbox start here *****
            if ($request->autoWebTransmissionCheckbox != null) {
                if ($request->has('allWebTransmissionlName') && $request->allWebTransmissionlName == 'allWebTransmissionValue') {
                } else if ($request->has('autoWebTransmissionCheckbox')) {
                    $Web_transmissions = $request->autoWebTransmissionCheckbox;
                    $query->where(function($subQuery) use ($Web_transmissions) {
                        foreach ($Web_transmissions as $transmission_info) {
                            $subQuery->orWhere('transmission', 'LIKE', '%' . $transmission_info . '%');
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
            // ***** web filter checkbox end here *****

            // if ($request->mobileColor != null) {
            //     $mobile_color = $request->mobileColor;
            //     $query->Where('exterior_color', 'LIKE', '%' . $mobile_color . '%');
            // }

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
            // $query->when($request->autoMaxBodyCheckbox !== null, function ($q) use ($request) {
            //     $q->orWhereNull('body_formated');
            // })->whereIn('body_formated', $request->autoMaxBodyCheckbox);

            if ($homeBodySearch != null && $homeBodySearch != 'new' && $homeBodySearch != 'used') {
                $query->where('body_formated', $homeBodySearch);
            }

            // if ($homeBodySearch != null && $homeBodySearch == 'used' ) {
            //     $query->where('type', '!=','new');
            // }

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
                $query->where('make', $request->webMakeFilterMakeInput);
            }
            if ($request->webModelFilterInput != null) {
                $query->where('model', $request->webModelFilterInput);
            }
            if ($request->totalLoanAmountCalculation != null) {
                $format_price  =intVal(str_replace(',','',$request->totalLoanAmountCalculation));
                $query->whereBetween('payment_price', [0, $format_price]);
            }

            // if ($request->webcolor != null) {
            //     $web_color = $request->webcolor;
            //     $query->where('exterior_color', 'LIKE', "$web_color");
            // }

            if ($request->mobileColorFilter != null) {
                $mobile_color = $request->mobileColorFilter;
                $query->where(function ($subQuery) use ($mobile_color) {
                    foreach ($mobile_color as $mobile_color) {
                        $subQuery->orWhere('exterior_color', 'LIKE', '%' . $mobile_color . '%');
                    }
                });
            }
            // if ($request->mobileColorFilter != null) {
            //     $mobile_color = $request->mobileColorFilter;
            //     $query->whereIn('exterior_color', 'LIKE', "$mobile_color");
            // }

            // mobile filter end here
        } else {
            if ($lowestValue != null) {
                $query->orderBy('price');
            }
            if ($lowestMileageValue != null) {
                $query->orderBy('miles');
            }
        }

        if ($homeLocationSearch != null) {
            $query->where('zip_code', $homeLocationSearch);
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

    public function blockedUserListing(array $ids)
    {
        $listings =  Inventory::whereIn('id',$ids)->get();

        foreach($listings as $listing)
        {
            $listing->is_visibility = ($listing->is_visibility == '0') ? '1' : '0';
            $listing->save();
        }
        return true;
    }


}
