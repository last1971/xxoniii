<?php

namespace App\Http\Controllers\API;

use App\Holiday;
use App\Theater;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TheaterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $from = substr($request->from, 0, 10);
        $to = substr($request->to, 0, 10);
        $days = (new Carbon($to))->diffInDays(new Carbon($from)) + 1;
        $holiday = '';
        $fromto = [ $from, $to, $from, $to ];
        if ($request->days == 'holidays') {
            $holiday = ' AND date in (select day from holidays where day between ? and ?) ';
            $fromto = [ $from, $to, $from, $to, $from, $to, $from, $to ];
            $days = Holiday::whereBetween('day', [$from, $to])->count();
        }
        if ($request->days == 'monday') {
            $holiday = ' AND date not in (select day from holidays where day between ? and ?) ';
            $fromto = [ $from, $to, $from, $to, $from, $to, $from, $to ];
            $days -= Holiday::whereBetween('day', [$from, $to])->count();
        }
        if ($days == 0) {
            return [];
        }
        $kpls = DB::select('select a.id, a.name, a.hall_title, a.seat_count,
          (select sum(min_price * (seat_count - seat_free)) from schedules 
          where schedules.theater_id=a.id and schedules.hall_title=a.hall_title and date between ? and ? ' . $holiday . ') 
          as hall_amount,
          (a.seat_count - (select avg(seat_free) from schedules 
          where schedules.theater_id=a.id and schedules.hall_title=a.hall_title and date between ? and ? ' . $holiday . ')) 
          as hall_fullness
          from (select distinct theaters.id, theaters.name, schedules.hall_title, schedules.seat_count from theaters join schedules on schedules.theater_id = theaters.id where seat_count != 0) a 
        ', $fromto);
        $ret = [];
        foreach ($kpls as $kpl) {
            if (!isset($ret[$kpl->name])) {
                $ret[$kpl->name] = new \stdClass();
                $ret[$kpl->name]->rows = [];
                $ret[$kpl->name]->amount = 0;
                $ret[$kpl->name]->seats = 0;
                $ret[$kpl->name]->fullness = 0;
                $ret[$kpl->name]->halls = 0;
            }
            $ret[$kpl->name]->rows[] = [
                'amount' => $kpl->hall_amount / $days,
                'fullness' => $kpl->hall_fullness / $kpl->seat_count * 100,
                'seats' => $kpl->seat_count,
                'name' => $kpl->hall_title
            ];
            $ret[$kpl->name]->amount += $kpl->hall_amount;
            $ret[$kpl->name]->seats += $kpl->seat_count;
            $ret[$kpl->name]->fullness += $kpl->hall_fullness;
            $ret[$kpl->name]->halls++;
        }


        $kpls = DB::select('select a.id, a.name, a.hall_title, a.seat_count,
          (select sum(min_price * (seat_count - seat_free)) from kinomax_schedules 
          where kinomax_schedules.theater_id=a.id and kinomax_schedules.hall_title=a.hall_title and date between ? and ? ' . $holiday . ') 
          as hall_amount,
          (a.seat_count - (select avg(seat_free) from kinomax_schedules 
          where kinomax_schedules.theater_id=a.id and kinomax_schedules.hall_title=a.hall_title and date between ? and ? ' . $holiday . ')) 
          as hall_fullness
          from (select distinct theaters.id, theaters.name, kinomax_schedules.hall_title, kinomax_schedules.seat_count 
          from theaters join kinomax_schedules on kinomax_schedules.theater_id = theaters.id where seat_count != 0) a 
        ', $fromto);

        foreach ($kpls as $kpl) {
            if (!isset($ret[$kpl->name])) {
                $ret[$kpl->name] = new \stdClass();
                $ret[$kpl->name]->rows = [];
                $ret[$kpl->name]->amount = 0;
                $ret[$kpl->name]->seats = 0;
                $ret[$kpl->name]->fullness = 0;
                $ret[$kpl->name]->halls = 0;
            }
            $ret[$kpl->name]->rows[] = [
                'amount' => $kpl->hall_amount / $days,
                'fullness' => $kpl->hall_fullness / $kpl->seat_count * 100,
                'seats' => $kpl->seat_count,
                'name' => $kpl->hall_title
            ];
            $ret[$kpl->name]->amount += $kpl->hall_amount;
            $ret[$kpl->name]->seats += $kpl->seat_count;
            $ret[$kpl->name]->fullness += $kpl->hall_fullness;
            $ret[$kpl->name]->halls++;
        }

        $kpls = DB::select('select a.hall_title, a.seat_count,
          (select sum(min_price * (seat_count - seat_free)) 
          from bolshoi_schedules where bolshoi_schedules.hall_title=a.hall_title and date between ? and ? ' . $holiday . ') as hall_amount,
          (a.seat_count - (select avg(seat_free)  
          from bolshoi_schedules where bolshoi_schedules.hall_title=a.hall_title and date between ? and ? ' . $holiday . ')) as hall_fullness
          from (select distinct bolshoi_schedules.hall_title, bolshoi_schedules.seat_count from bolshoi_schedules 
          where seat_count != 0 and bolshoi_schedules.hall_title is not null) a ',
            $fromto );

        $ret['Большой'] = new \stdClass();
        $ret['Большой']->rows = [];
        $ret['Большой']->amount = 0;
        $ret['Большой']->seats = 0;
        $ret['Большой']->fullness = 0;
        $ret['Большой']->halls = 0;
        foreach ($kpls as $kpl) {
            $ret['Большой']->rows[] = [
                'amount' => $kpl->hall_amount / $days,
                'fullness' => $kpl->hall_fullness / $kpl->seat_count * 100,
                'seats' => $kpl->seat_count,
                'name' => $kpl->hall_title
            ];
            $ret['Большой']->amount += $kpl->hall_amount;
            $ret['Большой']->seats += $kpl->seat_count;
            $ret['Большой']->fullness += $kpl->hall_fullness;
            $ret['Большой']->halls++;
        }

        foreach ($ret as $upd) {
            $upd->amount = $upd->amount / $days;
            $upd->fullness = $upd->fullness / $upd->seats * 100;
        }
        return $ret;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
