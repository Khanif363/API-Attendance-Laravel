<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Traits\ImageStorage;
use Carbon\Carbon;
use Illuminate\Http\Response;

class AttendanceController extends Controller
{
    use ImageStorage;

   public function store(AttendanceRequest $request) {
       $photo = $request->file('photo');
       $attendanceType = $request->type;
       $userAttendanceToday = $request->user()
        ->attendances()
        ->whereDate('created_at', Carbon::today())
        ->first();

        if($attendanceType == 'in') {
            if(!$userAttendanceToday) {
                $attendance = $request
                    ->user()
                    ->attendances()
                    ->create([
                    'status' => false,
                ]);
                $attendance->details()->create([
                    'type'          => 'in',
                    'long'          => $request->long,
                    'lat'           => $request->lat,
                    'address'       => $request->address,
                    'photo'         => $this->uploadImage($photo, $request->user()->name, 'attendance'),
                ]);
                return response()->json([
                    'message' => 'Attendance Successfully',
                ], Response::HTTP_CREATED);
            }

            return response()->json([
                'message' => 'You have already checked in today',
            ], Response::HTTP_OK);
        }

        if($attendanceType == 'out') {
            if($userAttendanceToday) {
                if($userAttendanceToday->status) {
                    return response()->json([
                        'message' => 'You have already checked out today',
                    ], Response::HTTP_OK);
                }

                $userAttendanceToday->update([
                    'status' => true,
                ]);

                $userAttendanceToday->details()->create([
                    'type'          => 'out',
                    'long'          => $request->long,
                    'lat'           => $request->lat,
                    'address'       => $request->address,
                    'photo'         => $this->uploadImage($photo, $request->user()->name, 'attendance'),
                ]);

                return response()->json([
                    'message' => 'Attendance Successfully',
                ], Response::HTTP_CREATED);
            }

            return response()->json([
                'message' => 'Please check in first',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
   }
}
