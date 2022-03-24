<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\CalendarEvent;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()) {  
            $data = CalendarEvent::whereDate('event_start', '>=', $request->start)
                ->whereDate('event_end',   '<=', $request->end)
                ->get(['id', 'event_title', 'event_start', 'event_end']);
            return response()->json($data);
        }
        return view('calendar');
    }
 

    public function manageEvents(Request $request)
    {
        $id = Auth::id();
        switch ($request->type) {
           case 'create':
              $calendarEvent = CalendarEvent::create([
                  'event_title' => $request->event_title,
                  'event_start' => $request->event_start,
                  'event_end' => $request->event_end,
                  'user_id'=>$id,
                  'heure_deb'=>$request->heure_deb,
                  'heure_fin'=>$request->heure_fin
              ]);
 
              return response()->json($calendarEvent);
             break;
  
           case 'edit':
              $calendarEvent = CalendarEvent::find($request->id)->update([
                  'event_title' => $request->title,
                  'event_start' => $request->start,
                  'event_end' => $request->end,
              ]);
 
              return response()->json($calendarEvent);
             break;
  
           case 'delete':
              $calendarEvent = CalendarEvent::find($request->id)->delete();
  
              return response()->json($calendarEvent);
             break;
            case 'resize':
                $calendarEvent=CalendarEvent::find($request->id)->update([
                  'event_start' => $request->start,
                  'event_end' => $request->end,
                ]);
                return response()->json($calendarEvent);
                break;
             
           default:
             break;
        }
    }
    public function get_user_infos(Request $request){
        $id=Auth::id();
        $user_infos=CalendarEvent::where('user_id', $id)->get();
        return json_encode($user_infos);
    }
}