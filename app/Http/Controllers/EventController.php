<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventController extends Controller
{
    public function showPage()
    {
        return view('calendar');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Event[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Event::with('hasManyEventSchedule')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*Empty table*/
        Event::truncate();
        EventSchedule::truncate();
        /*Save Events*/
        $newEvent = Event::create($request->all());

        $selectedDates = $this->getSelectedDates($request->date_from, $request->date_to, $request->days);
        $eventSchedule = [];
        foreach ($selectedDates as $dates) {
            $eventSchedule[] = [
                'event_id' => $newEvent->id,
                'date' => $dates
            ];
        }
        EventSchedule::insert($eventSchedule);
        return response()->json([
            'data' => Event::with('hasManyEventSchedule')->get(),
            'message' => 'Successfully added new event',
            'status' => Response::HTTP_CREATED
        ]);
    }

    public function getSelectedDates($dateStart, $dateend, array $days)
    {
        $dateend = Carbon::parse($dateend)->addDay();
        $period = new \DatePeriod(
            new \DateTime($dateStart), new \DateInterval('P1D'), (new \DateTime($dateend))
        );
        $dates = iterator_to_array($period);

        $selectedDates = array();
        foreach ($dates as $val) {
            $date = $val->format('Y-m-d'); //format date
            $get_name = date('l', strtotime($date)); //get week day
            $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars
            foreach ($days as $key => $day) {
                if ($key === $day_name && $day === true) {
                    $selectedDates[] = $date;
                }
            }
        }
        return $selectedDates;
    }
}
