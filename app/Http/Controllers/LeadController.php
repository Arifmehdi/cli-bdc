<?php

namespace App\Http\Controllers;

use App\Interface\InventoryServiceInterface;
use App\Interface\LeadServiceInterface;
use App\Mail\ADFMail;
use App\Mail\AdminLeadSendMail;
use App\Mail\ConfirmPassword;
use App\Models\Contact;
use App\Models\Inventory;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\LeadSendMail;
use App\Mail\MessageSendEmail;
use App\Mail\ShareMail;
use App\Models\Invoice;
use App\Models\MainInventory;
use App\Models\Membership;
use App\Models\Message;
use App\Models\Notification;
use App\Traits\Notify;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class LeadController extends Controller
{
    use Notify;
    public function __construct(private LeadServiceInterface $leadService, private InventoryServiceInterface $inventoryService) {}

    public function lead_info(Request $request)
    {

        // $temp_car_details = Inventory::find($request->id);
        $temp_car_details = MainInventory::find($request->id);
        return $temp_car_details;
    }

    public function contactShow(Request $request, $id = null)
    {
        if ($id != null) {
            $data = Notification::find($id);
            $data->is_read = '1';
            $data->save();
        }
        if ($request->showTrashed == 'true') {
            $info = Contact::onlyTrashed()->orderBy('id', 'desc');
        } else {
            $info = Contact::orderBy('created_at', 'desc');
        }
        $rowCount = Contact::count();
        $trashedCount = Contact::onlyTrashed()->count();

        if ($request->ajax()) {
            return dataTables::of($info)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })

                ->addColumn('check', function ($row) {
                    $html = '<div class=" text-center">
                        <input type="checkbox" name="contact_id[]" value="' . $row->id . '" class="mt-2 check1 check-row">

                    </div>';
                    return $html;
                })
                ->addColumn('message', function ($row) {
                    $originalMessage = $row->message;
                    $trimmedMessage = substr($originalMessage, 0, 50); // Removed the extra closing parenthesis
                    $html = '<p>' . $trimmedMessage . '</p>';
                    return $html;
                })

                ->addColumn('action', function ($row) {

                    if ($row->trashed()) {
                        $html = '<a href="' . route('admin.contact.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
                            '<a href="' . route('admin.contact.permanent.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                    } else {
                        $html = '<a data-id="' . $row->id . '" style="margin-right:6px !important" class="btn btn-success btn-sm view-contact"><i  class="fa fa-eye"></i></a>' .
                            '<a data-id= "' . $row->id . '" class="btn btn-danger btn-sm delete-contact"><i  class="fa fa-trash"></i></a>';
                    }
                    return $html;
                })

                ->rawColumns(['action', 'message', 'check'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }
        return view('backend.admin.contact.contact_show');
    }

    public function singleContact(Request $request)
    {
        $singleContact = Contact::find($request->id);

        if ($singleContact) {
            return response()->json(['singleContact' => $singleContact]);
        } else {
            return response()->json(['error' => 'Contact not found'], 404);
        }
    }

    public function deleteContact(Request $request)
    {
        $contact = Contact::find($request->id);
        $contact->delete();
        return response()->json([
            'status' => 'success',
            'message' => "Contact Delete Successfully"
        ]);
    }

    public function deleteContactAll(Request $request)
    {
        if (isset($request->contact_id)) {
            if ($request->action_type == 'move_to_trash') {
                foreach ($request->contact_id as $id) {
                    $item = Contact::find($id);
                    $item->delete($item);
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Messages deleted successfully'
                ]);
            } elseif ($request->action_type == 'restore_from_trash') {

                foreach ($request->contact_id as $id) {
                    $item = Contact::withTrashed()->find($id);
                    $item->restore($item);
                }
                return response()->json('Messages are restored successfully');
            } elseif ($request->action_type == 'delete_permanently') {

                foreach ($request->contact_id as $id) {
                    $item = Contact::onlyTrashed()->find($id);
                    $item->forceDelete($item);
                }

                return response()->json('Messages are permanently deleted successfully');
            } else {
                return response()->json('Action is not specified.');
            }
        } else {
            return response()->json(['message' => 'No Item is Selected.'], 401);
        }
    }

    public function contactRestore(Request $request, $id)
    {

        Contact::withTrashed()->find($id)->restore();
        return response()->json('Message restored successfully');
    }

    public function contactPermanentDelete(Request $request, $id)
    {
        $item = Contact::onlyTrashed()->find($id);
        $item->forceDelete();

        return response()->json('Message is permanently deleted successfully');
    }
    public function lead(Request $request)
    {

        $sup_id = User::where('role_id', 1)->first()->id;
        // $inventory = Inventory::with('dealer')->where('id', $request->inventories_id)->first();
        $inventory = MainInventory::with('dealer','additionalInventory')->where('id', $request->inventories_id)->first();

        $image_str = str_replace(['[', ' ', "'"], '', $inventory->additionalInventory->local_img_url);
        $images = explode(',', $image_str);
        $lead = new Lead();
        $message = new Message();
        $userInfo = [];

        if ($request->ajax()) {
            Log::info('Captcha input: ' . $request->input('mathcaptcha'));
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required',
                'description' => 'required',
                'mathcaptcha' => ['required', 'mathcaptcha'],
            ], [
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'phone.required' => 'Phone is required.',
                'description.required' => 'The description field is required.',
                'mathcaptcha.required' => 'captcha is required.',
                'mathcaptcha.mathcaptcha' => 'Answer is incorrect.',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $user = new User();
                $user->name = $request->first_name . ' ' . $request->last_name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->save();
                $userInfo = [
                    'name' => $user->name,
                    'id' => $user->id,
                    'email' => $user->email,
                ];

                $lead->user_id = $user->id;
                $message->sender_id = $user->id;
                $this->SendMail($userInfo);
            } else {

                if (empty($user->name)) {
                    $user->name = $request->first_name . ' ' . $request->last_name;
                }
                if (empty($user->phone)) {
                    $user->phone = $request->phone;
                }
                $user->save();


                $lead->user_id = $user->id;
                $message->sender_id = $user->id;
                $userInfo = [
                    'name' => $user->name,
                    'id' => $user->id,
                    'email' => $user->email,
                ];
            }
            // Fill in the lead details
            $lead->inventory_id = $request->inventories_id;
            $lead->dealer_id = $inventory->dealer->id;
            $lead->date = now()->format('Y-m-d');
            $lead->description = $request->description;
            $lead->year = $request->year;
            $lead->make = $request->make;
            $lead->model = $request->model;
            $lead->mileage = $request->mileage;
            $lead->color = $request->color;
            $lead->vin = $request->vin;
            $lead->save();

            $notification_title = 'Consumer send a lead';
            $notification_message = 'A lead come from website';
            $notification_call_back_url = route('admin.lead.show');
            $notification_category = 'communication';
            $notificatioN_auth_id = $lead->user_id;

            $this->saveNewNotification($notification_title, $notification_message, $notification_call_back_url, $notificatioN_auth_id, $notification_category);

            $message->message = $request->description;

            $message->receiver_id = $sup_id;
            $message->lead_id = $lead->id;
            $message->is_seen = 1;
            $message->save();


            $userInfo['image'] = $images[0];
            $this->LeadMail($userInfo);

            return response()->json([
                'status' => 'success',
                'message' => 'Message Sent Successfully!'
            ]);
        } else {
            return ('hello');
        }
    }


    private function SendMail($userInfo)
    {
        $data = [
            'name' => $userInfo['name'],
            'email' => $userInfo['email'],
            'id' => $userInfo['id']
        ];
        Mail::to($userInfo['email'])->send(new ConfirmPassword($data));
    }



    private function LeadMail($userInfo)
    {
        $lead_details = Lead::with('mainInventory')->where('user_id', $userInfo['id'])->orderBy('id', 'desc')->first();

        $year = $lead_details->mainInventory->year;
        $make = $lead_details->mainInventory->make;
        $model = $lead_details->mainInventory->model;
        $image = $lead_details->mainInventory->additionalInventory->local_img_url;
        $explode_image = explode(',', $image);
        // dd( asset($explode_image[0]));
        $price = number_format($lead_details->mainInventory->price);
        $stock = $lead_details->mainInventory->stock;
        $miles = number_format($lead_details->mainInventory->miles);
        $tradeInYear = $lead_details->year;
        $tradeInMake = $lead_details->make;
        $tradeInModel = $lead_details->model;
        $tradeInVin = $lead_details->vin;
        $tradeInMiles = number_format($lead_details->mileage);
        $tradeInColor = $lead_details->color;
        $ext_color_generic = $lead_details->mainInventory->exterior_color;
        $data = [
            'id' => $userInfo['id'],
            'name' => $userInfo['name'],
            'email' => $userInfo['email'],
            'description' => $lead_details->description,
            'year'          => [$year, $tradeInYear],
            'make'          => [$make, $tradeInMake],
            'model'          => [$model, $tradeInModel],
            'price'             => $price,
            'stock'             => $stock,
            'miles'             => [$miles, $tradeInMiles],
            'vin'             => $tradeInVin,
            'color'             => [$ext_color_generic, $tradeInColor],
            'image'     => $explode_image,
        ];

        Mail::to($userInfo['email'])->send(new LeadSendMail($data));
    }



    public function leadShow(Request $request, $id = null)
    {


        if ($id != null) {
            $notification = Notification::find($id);
            $notification->is_read = '1';
            $notification->save();
        }
        // $inventory = Inventory::query();
        $inventory = MainInventory::query();
        $data['inventory_make'] = $inventory->distinct('make')->pluck('id','make')->toArray();

        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'dealer');
        })
        ->whereNotNull('city')
        ->where('city', '!=', '')
        ->whereNotNull('state')
        ->where('state', '!=', '')
        ->get(['id', 'name', 'city', 'state']);
        $data['inventory_dealer_name'] = $users->pluck('id', 'name')->toArray();
        $data['inventory_dealer_city'] = $users->pluck('id', 'city')->toArray();
        $data['inventory_dealer_state'] = $users->pluck('id', 'state')->toArray();

        ksort($data['inventory_make']);
        ksort($data['inventory_dealer_name']);
        ksort($data['inventory_dealer_city']);
        ksort($data['inventory_dealer_state']);

        // $cars = $this->inventoryService->all()->select(['title','user_id','id', 'year', 'make', 'model', 'trim', 'price', 'stock', 'local_img_url',])->get();
        $leads = Lead::with('customer','mainInventory')->orderBy('id', 'desc')->get();
        // $salesmans = Sale::all();
        // dd($leads, $data, $inventory->get()[0]);
        if ($request->showTrashed == 'true') {
            $info = $this->leadService->getTrashedItem();
        } else {
            $info = $this->leadService->getItemByFilter($request);
        }

        $rowCount = $this->leadService->getRowCount();
        $trashedCount = $this->leadService->getTrashedCount();


        if ($request->ajax()) {
            return datatables::of($info)->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })->addColumn('check', function ($row) {
                    $html = '<div class=" text-center">
                        <input type="checkbox" name="lead_id[]" value="' . $row->id . '" class="mt-2 check1 check-row">
                        <input type="hidden" name="dealer_id[]" value="' . $row->dealer_id . '" class="mt-2 check1 check-row">

                    </div>';
                    return $html;
                })->addColumn('title', function ($row) {
                    return $row->mainInventory->title ?? 'Null';
                })
                ->addColumn('make', function ($row) {
                    return $row->mainInventory->make ?? 'Null';
                })
                ->addColumn('dealer_name', function ($row) {
                    $dealerName = isset($row->dealer) ? explode(' in ', $row->dealer->name)[0] : 'Null';
                    $url = route('admin.dealer.profile', $row->dealer_id);
                    return "<a href='{$url}'>{$dealerName}</a>";
                })

                ->addColumn('state', function ($row) {
                    return $row->dealer->state ?? 'Null';
                })
                ->addColumn('city', function ($row) {
                    return $row->dealer->city ?? 'Null';
                })

                ->addColumn('name', function ($row) {
                    return $row->customer->name ?? 'Null';
                })
                ->addColumn('email', function ($row) {
                    return $row->customer->email ?? 'Null';
                })
                ->addColumn('phone', function ($row) {
                    return $row->customer->phone ?? 'Null';
                })
                ->addColumn('status', function ($row) {
                    $html = '<p>' . ($row->status == 1 ? 'Active' : 'Inactive') . '</p>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    if ($row->trashed()) {
                        $html = '<a href="' . route('admin.lead.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
                            '<a href="' . route('admin.lead.permanent.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                    } else {
                        $html = '<a data-id="' . $row->id . '" href="javascript:void(0);" style="color:white; margin-right: 6px !important" class="btn btn-info btn-sm message_view common_read"><i class="fas fa-comment-alt"></i></a>' .
                            '<a href="' . route('admin.single.lead.view', $row->id) . '" style="margin-right: 6px !important" class="btn btn-success btn-sm lead_view common_read"><i class="fa fa-eye"></i></a>' .
                            '<a href="javascript:void(0);" style="margin-right: 6px !important" class="btn btn-warning btn-sm common_read send-adf-mail" data-id="' . $row->id . '" title="ADF Mail"><i class="fa fa-paper-plane"></i></a>' .
                            '<a href="javascript:void(0);" style="margin-right: 6px !important" class="btn btn-primary btn-sm common_read send-mail" data-id="' . $row->id . '" title="Mail"><i class="fa fa-envelope"></i></a>' .
                            '<a href="javascript:void(0);" class="btn btn-danger common_read btn-sm lead_delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';
                    }
                    return $html;
                })
                ->rawColumns(['action', 'status', 'check','dealer_name'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }
        return view('backend.admin.lead.lead_show',compact('leads'),$data);
    }

    public function singleLeadShow($id)
    {
        $lead = Lead::with('mainInventory', 'customer', 'dealer')->find($id);
        $lead->status = '1';
        $lead->save();
        return view('backend.admin.lead.single_lead_view', compact('lead'));
    }

    public function deleteLead(Request $request)
    {
        $contact = Lead::find($request->id);
        $contact->delete();
        return response()->json([
            'status' => 'success',
            'message' => "lead Delete Successfully"
        ]);
    }

    public function messageShow(Request $request)
    {

        $data = Message::where('lead_id', $request->id)->get();
        $lead_info = Lead::find($data[0]->lead_id);

        // return view('backend.admin.message.message', compact('data', 'lead_info'));
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'lead_info' => $lead_info
        ]);
    }

    public function messageSend(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:users,id', // Assuming receiver_id corresponds to a user ID in the users table
            'lead_id' => 'required|exists:leads,id', // Assuming lead_id corresponds to a lead ID in the leads table
            'message' => 'required|string', // Assuming message is a string and required
        ]);


        try {
            // Create a new message instance
            $message = new Message();
            $message->receiver_id = $validatedData['receiver_id'];
            $message->sender_id = auth()->id(); // Set sender as the currently authenticated user
            $message->lead_id = $validatedData['lead_id'];
            $message->message = $validatedData['message'];
            $message->is_seen = 0;

            if (!$message->save()) {
                // Handle failure to save the message
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to send the message. Please try again later.',
                ], 500);
            }

            // Mark all messages related to the same lead as unseen
            Message::where('lead_id', $validatedData['lead_id'])->update(['is_seen' => 0]);

            // Trigger email notification
            $this->MessageSendEmail($validatedData['lead_id']);

            // Return success response
            return response()->json([
                'status' => 'success',
                'data' => $message,
                'message' => 'Message sent successfully.',
            ], 200);

        } catch (\Exception $e) {
            // Handle unexpected exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }


    }




    private function MessageSendEmail($lead_id)
    {
        $lead_details = Lead::with('mainInventory','customer')->where('id', $lead_id)->first();

        $year = $lead_details->mainInventory->year;
        $make = $lead_details->mainInventory->make;
        $model = $lead_details->mainInventory->model;
        $price = number_format($lead_details->mainInventory->price);
        $stock = $lead_details->mainInventory->stock;
        $miles = number_format($lead_details->mainInventory->miles);
        $tradeInYear = $lead_details->year;
        $tradeInMake = $lead_details->make;
        $tradeInModel = $lead_details->model;
        $tradeInVin = $lead_details->vin;
        $tradeInMiles = number_format($lead_details->mileage);
        $tradeInColor = $lead_details->color;
        $ext_color_generic = $lead_details->mainInventory->exterior_color;
        $img = $lead_details->mainInventory->additionalInventory['local_img_url'];
        $image =  explode(',', $img);
        $image = str_replace(['[', "'"], '', $image);
        $data = [
            'id' => $lead_details->customer->id,
            'name' =>  $lead_details->customer->name,
            'email' =>  $lead_details->customer->email,
            'description' => $lead_details->description,
            'year'          => [$year, $tradeInYear],
            'make'          => [$make, $tradeInMake],
            'model'          => [$model, $tradeInModel],
            'price'             => $price,
            'stock'             => $stock,
            'miles'             => [$miles, $tradeInMiles],
            'vin'             => $tradeInVin,
            'color'             => [$ext_color_generic, $tradeInColor],
            'image'     => $image,
        ];


        Mail::to($lead_details->customer->email)->send(new MessageSendEmail($data));
    }



    public function emailSend(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mail' => 'required|email',

        ], [
            'mail.required' => 'The email field is required.',
            'mail.email' => 'The email must be a valid email address.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $lead_details = Lead::with('inventory', 'customer')->find($request->send_id);
        $dealerCheck = User::find($lead_details->dealer_id);
        if ($dealerCheck) {
            $dealerCheck->hashkey = bin2hex(random_bytes(8));
            $dealerCheck->save();
            $hashkey = $dealerCheck->hashkey;
        } else {

            $hashkey = null;
        }

        if ($dealerCheck->package != '0') {
            $customer_name = $lead_details->customer->name ?? '';
            $customer_email = $lead_details->customer->email ?? '';
            $phone = $lead_details->customer->phone ?? '';
        }

        // Prepare data for email

        $year = $lead_details->inventory->year;
        $make = $lead_details->inventory->make;
        $model = $lead_details->inventory->model;
        $price = number_format($lead_details->inventory->price);
        $stock = $lead_details->inventory->stock;
        $miles = number_format($lead_details->inventory->miles);
        $tradeInYear = $lead_details->year;
        $tradeInMake = $lead_details->make;
        $tradeInModel = $lead_details->model;
        $tradeInVin = $lead_details->vin;
        $tradeInMiles = number_format($lead_details->mileage);
        $tradeInColor = $lead_details->color;
        $ext_color_generic = $lead_details->inventory->exterior_color;
        $img = $lead_details->inventory['local_img_url'];
        $image =  explode(',', $img);
        $image = str_replace(['[', "'"], '', $image);
        $email_message = $lead_details->description;
        $data = [
            'customer_name'     => $customer_name ?? "***",
            'phone'             => $phone ?? "***",
            'year'              => [$year, $tradeInYear],
            'make'              => [$make, $tradeInMake],
            'model'             => [$model, $tradeInModel],
            'price'             => $price,
            'stock'             => $stock,
            'miles'             => [$miles, $tradeInMiles],
            'vin'               => $tradeInVin,
            'color'             => [$ext_color_generic, $tradeInColor],
            'email_message'     => $email_message,
            'image'             => $image[0],
            'customer_email'    => $customer_email ?? "***",
            'hashkey'           => $hashkey,
        ];

        // Send email
        Mail::to($request->mail)->send(new AdminLeadSendMail($data));


        // Return success response
        return response()->json(['status' => 'success', 'message' => 'Mail Sent Successfully']);
    }


    public function adfemailSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'adf_mail' => 'required|email',

        ], [
            'adf_mail.required' => 'The email field is required.',
            'adf_mail.email' => 'The email must be a valid email address.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $lead_details = Lead::with('inventory', 'customer')->find($request->send_adf_id);
        $dealerCheck = User::find($lead_details->dealer_id);

        if ($dealerCheck) {
            $dealerCheck->hashkey = bin2hex(random_bytes(8));
            $dealerCheck->save();
            $hashkey = $dealerCheck->hashkey;
        } else {

            $hashkey = null;
        }

        if ($dealerCheck->package != '0') {
            $customer_name = $lead_details->customer->name ?? '';
            $customer_email = $lead_details->customer->email ?? '';
            $phone = $lead_details->customer->phone ?? '';
        }
        // // Prepare data for email

        $year = $lead_details->inventory->year;
        $make = $lead_details->inventory->make;
        $model = $lead_details->inventory->model;
        $price = number_format($lead_details->inventory->price);
        $stock = $lead_details->inventory->stock;
        $miles = number_format($lead_details->inventory->miles);
        $tradeInYear = $lead_details->year;
        $tradeInMake = $lead_details->make;
        $tradeInModel = $lead_details->model;
        $tradeInVin = $lead_details->vin;
        $tradeInMiles = number_format($lead_details->mileage);
        $tradeInColor = $lead_details->color;
        $ext_color_generic = $lead_details->inventory->exterior_color;
        $img = $lead_details->inventory['local_img_url'];
        $image =  explode(',', $img);
        $image = str_replace(['[', "'"], '', $image);
        $email_message = $lead_details->description;
        $data = [
            'customer_name'     => $customer_name ?? "***",
            'phone'             => $phone ?? "***",
            'year'              => [$year, $tradeInYear],
            'make'              => [$make, $tradeInMake],
            'model'             => [$model, $tradeInModel],
            'price'             => $price,
            'stock'             => $stock,
            'miles'             => [$miles, $tradeInMiles],
            'vin'               => $tradeInVin,
            'color'             => [$ext_color_generic, $tradeInColor],
            'email_message'     => $email_message,
            'image'             => $image[0],
            'customer_email'    => $customer_email ?? "***",
            'hashkey'           => $hashkey,
        ];
        
        $leadData = [
            'id' => $request->send_adf_id,
            'vehicle_year' => [$year, $tradeInYear],
            'vehicle_make' => [$make, $tradeInMake],
            'vehicle_model' => [$model, $tradeInModel],
            'customer_name' => $customer_name ?? "***",
            'customer_phone' => $phone ?? "***",
            'customer_email' => $request->adf_mail,
        ];
        Mail::to($request->adf_mail)->send(new ADFMail($leadData));
        // // Send email
        // Mail::to($request->mail)->send(new AdminLeadSendMail($data));


        // Return success response
        return response()->json(['status' => 'success', 'message' => 'ADF Mail Sent Successfully']);
    }

    public function bulkAction(Request $request)
    {


        // dd($request->admin_inventory_id, $request->action_type);
        if (isset($request->lead_id)) {
            if ($request->action_type == 'move_to_trash') {
                $leads = $this->leadService->bulkTrash($request->lead_id);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Lead deleted successfully'
                ]);
            } elseif ($request->action_type == 'invoice') {

                $selectedData = $request->lead_id;
                $existingInvoices = Lead::with('dealer')->whereIn('id', $selectedData)->get();
                // Track the dealers for the leads
                $dealers = [];
                foreach ($existingInvoices as $invoice) {
                    $dealerId = $invoice->dealer->id;
                    if (!in_array($dealerId, $dealers)) {
                        $dealers[] = $dealerId;
                    }


                    // If more than one unique dealer is found, return an error message
                    if (count($dealers) > 1) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Only one dealer lead can be added at a time.'
                        ]);
                    }
                }

                $invoice = Invoice::where('is_cart','0')->where('type','Lead')->first();
                if($invoice)
                {
                    if($invoice->user_id != $dealers[0])
                    {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Only one dealer lead can be added at a time.'
                        ]);
                    }
                }

                $existingInvoices = Invoice::whereIn("lead_id", $selectedData)->get();
                $existingDataIds = $existingInvoices->pluck("lead_id")->toArray();
                $membership = Membership::where('type','lead')->first();
                $newData = array_diff($selectedData, $existingDataIds);
                if (!empty($newData)) {
                    $invoicesToInsert = [];

                    foreach ($newData as $id) {
                        $invoicesToInsert[] = [
                            'lead_id' => $id,
                            'price' => $membership->membership_price,
                            'user_id' => $dealers[0],
                            'type' => 'Lead',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    Invoice::insert($invoicesToInsert);
                    return response()->json(['status' => 'success', 'message' => 'Added to cart successfully!']);
                    // return redirect()->back()->with('status', 'success')->with('message', 'Added to cart successfully!');
                } else {
                    return response()->json(['status' => 'error', 'message' => 'All data already checked']);
                    // return redirect()->back()->with('status', 'error')->with('message', );
                }

                // Converting the view content to JSON
            } elseif ($request->action_type == 'restore_from_trash') {
                $leads = $this->leadService->bulkRestore($request->lead_id);

                return response()->json('Leads are restored successfully');
            } elseif ($request->action_type == 'delete_permanently') {
                $leads = $this->leadService->bulkPermanentDelete($request->lead_id);

                return response()->json('Leads are permanently deleted successfully');
            } else {
                return response()->json('Action is not specified.');
            }
        } else {
            return response()->json(['message' => 'No Item is Selected.'], 401);
        }
    }

    public function restore($id)
    {
        $inventory = $this->leadService->restore($id);
        return response()->json('Lead restored successfully');
    }

    public function permanentDelete($id)
    {
        $inventory = $this->leadService->permanentDelete($id);

        return response()->json('Lead is permanently deleted successfully');
    }

    public function leadSeen(Request $request)
    {
        $lead =  Lead::where('id', $request->id)->first();
        $lead->status = '1';
        $lead->save();
        return response()->json(['status' => 'success']);
    }
    public function share(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',

        ], [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        // $inventory = Inventory::where('id', $request->id)->first();
        $inventory = MainInventory::with(['additionalInventory:id,main_inventory_id,local_img_url'])
        ->where('id', $request->id)
        ->first(['id', 'deal_id', 'title', 'year', 'make', 'model', 'price', 'stock', 'miles', 'vin', 'exterior_color']);
    
        
        $img = $inventory->additionalInventory->local_img_url;
        $image =  explode(',', $img);
        $image = str_replace(['[', "'"], '', $image);
        $description = "I wanted to share something exciting with you. Recently got an incredible car, and I thought you’d love to hear about it. If you're looking for the best car in terms of reliability, performance, and overall value, the {$inventory->title} stands out as a top choice.";



        $data = [
            'id' => $request->id,
            'name' => $request->name,
            'email' => $request->email,
            'description' => $description,
            'year'          => $inventory->year,
            'make'          => $inventory->make,
            'model'          => $inventory->model,
            'price'             => number_format($inventory->price),
            'stock'             => $inventory->stock,
            'miles'             => number_format($inventory->miles),
            'vin'             => $inventory->vin,
            'color'             => $inventory->exterior_color,
            'image'     => $image[0],
        ];

        Mail::to($request->email)->send(new ShareMail($data));


        return response()->json([
            'status' => 'success',
            'message' => 'Mail Sent Successfully'
        ]);
    }


    public function searchVichele(Request $request)
    {
        try
        {
            if ($request->ajax()) {
                $cars = $this->inventoryService->all()->where('model', 'LIKE', '%' . $request->search . '%')
                    ->orwhere('make', 'LIKE', '%' . $request->search . '%')
                    ->orwhere('year', 'LIKE', '%' . $request->search . '%')
                    ->orwhere('vin', 'LIKE', '%' . $request->search . '%')
                    ->get();
                return response()->json(['cars' => $cars]);
            }

        }catch(Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


    public function selectCar(Request $request)
    {
        $car = Inventory::where('id', $request->car_id)->select(['title','id', 'year', 'make', 'model', 'trim', 'price','vin', 'stock', 'local_img_url',])->first();

        $car = [
            'id' => $car->id,
            'title' => $car->title,
            'image' => $car->local_img_url,
            'stock' => $car->stock,
        ];
        return response()->json(['car' => $car]);
    }


    public function adminLeadStore(Request $request)
    {

        if ($request->ajax()) {


            $lead = new Lead();
            $message = new Message();
            $dealer_info =  Inventory::find($request->vechile_id);
            $user = User::find($request->customer_id);
            if (!$user) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [

                    'first_name' => 'required|string',
                    'last_name' => 'required|string',
                    'email' => 'required|email|unique:users',
                    'phone' => 'required',
                    'lead_type' => 'required',
                    'source' => 'required',

                ]);
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()]);
                }
                // insert data user table
                $user = new User();
                $user->name = $request->first_name . ' ' . $request->last_name;
                $user->fname = $request->first_name;
                $user->lname = $request->last_name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->salesperson = $request->salesperson;
                $user->phone_type = $request->phone_type;
                $user->contact_type = $request->contact_type;
                $user->save();

                // insert data Lead table
                $lead->user_id = $user->id;
                // insert data lead message table
                $userInfo = [
                    'name' => $user->name,
                    'id' => $user->id,
                    'email' => $user->email,
                ];
                $message->sender_id = $user->id;
                $this->SendMail($userInfo);

            } else {
                $validator = Validator::make($request->all(), [

                    'lead_type' => 'required',
                    'source' => 'required',

                ]);
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()]);
                }

                $lead->user_id = $request->customer_id;
            }

            $userInfo = [
                'name' => $user->name ?? '',
                'id' => $user->id,
                'email' => $user->email ?? '',
            ];

            $lead->dealer_id  = $dealer_info->deal_id;
            $lead->lead_type = $request->lead_type;
            $lead->source = $request->source;
            $lead->inventory_id = $request->vechile_id;
            $lead->description = $request->note;
            $lead->date = now()->format('Y-m-d');
            $lead->save();
            $this->LeadMail($userInfo);
            //message table save data
            $message->message = $request->note;
            $message->receiver_id = Auth::id();
            $message->lead_id = $lead->id;
            $message->is_seen = 1;
            $message->save();
            return response()->json(['message' => 'Lead Create successfully']);
        }
    }


}
