<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facility_id' => 'required|exists:facilities,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'tujuan_penggunaan' => 'required_without:keperluan|nullable|string|max:255',
            'keperluan' => 'required_without:tujuan_penggunaan|nullable|string|max:255',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $start = $this->start_time;
            $end = $this->end_time;

            if (!$start || !$end) {
                return;
            }

            // Cek kelipatan 30 menit & jam operasional 07.00-20.00
            if (!$this->isValidSlot($start)) {
                $validator->errors()->add('start_time', 'Waktu harus kelipatan 30 menit antara 07.00-20.00.');
            }

            if (!$this->isValidSlot($end)) {
                $validator->errors()->add('end_time', 'Waktu harus kelipatan 30 menit antara 07.00-20.00.');
            }

            // Cek bentrok jadwal (hanya yang berstatus 'disetujui')
            if ($this->facility_id && $this->tanggal) {
                $bentrok = Reservation::where('facility_id', $this->facility_id)
                    ->where('tanggal', $this->tanggal)
                    ->where('status', 'disetujui')
                    ->where(function ($q) use ($start, $end) {
                        $q->where('start_time', '<', $end)
                          ->where('end_time', '>', $start);
                    })
                    ->exists();

                if ($bentrok) {
                    $validator->errors()->add('start_time', 'Jadwal bentrok dengan reservasi lain.');
                }
            }
        });
    }

    /**
     * Cek apakah waktu kelipatan 30 menit dan berada dalam jam 07:00-20:00
     */
    private function isValidSlot(string $time): bool
    {
        if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            return false;
        }

        [$h, $m] = explode(':', $time);

        if ($m !== '00' && $m !== '30') {
            return false;
        }

        if ($time < '07:00' || $time > '20:00') {
            return false;
        }

        return true;
    }
}

