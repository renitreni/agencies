<?php

namespace App\Models;

use Database\Factories\CandidateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Tags\HasTags;

/**
 * php artisan scout:import 'App\Models\Candidate'
 */
class Candidate extends Model
{
    use Searchable;
    use HasFactory;
    use HasTags;

    protected $fillable = [
        'id',
        'agency_id',
        'position_1',
        'position_2',
        'position_3',
        'skills',
        'employer_id',
        'status',
        'salary',
        'position_selected',
        'date_hired',
        'agency_abroad_id',
        'deployed',
        'date_deployed',
        'passport',
        'place_issue',
        'dos',
        'doe',
        'remarks',
        'kin',
        'kin_relationship',
        'kin_contact',
        'kin_address',
        'applied_using',
        'agency_branch',
        'code',
        'iqama',
        'photo_url',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'fb_account',
        'contact_1',
        'contact_2',
        'address',
        'birth_date',
        'birth_place',
        'civil_status',
        'gender',
        'blood_type',
        'height',
        'weight',
        'religion',
        'language',
        'education',
        'spouse',
        'mother_name',
        'father_name',
        'agreed',
        'travel_status',
        'deleted_at',
        'created_at',
        'updated_at',
        'skills_other',
        'fullname',
    ];

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'candidates_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->only('first_name', 'last_name', 'passport');

        return $array;
    }

    public function employment()
    {
        return $this->hasMany(EmploymentHistory::class, 'candidate_id', 'id');
    }

    public function document()
    {
        return $this->hasMany(Document::class, 'candidate_id', 'id');
    }

    public function documentCV()
    {
        return $this->hasMany(Document::class, 'candidate_id', 'id');
    }

    public function documentPic1x1()
    {
        return $this->hasOne(Document::class, 'candidate_id', 'id')->where('type', 'pic1x1');
    }

    public function documentPicFull()
    {
        return $this->hasOne(Document::class, 'candidate_id', 'id')->where('type', 'picfull');
    }

    public function agency()
    {
        return $this->hasOne(Agency::class, 'id', 'agency_id');
    }

    public function employer()
    {
        return $this->hasOne(Information::class, 'user_id', 'employer_id');
    }

    public function affiliates()
    {
        return $this->hasOne(Information::class, 'user_id', 'agency_abroad_id');
    }

    public static function belongsToAgency($id, $agency_id)
    {
        return (new static())->where('id', $id)->where('agency_id', $agency_id)->count() > 0;
    }

    public static function belongsToEmployer($id, $agency_id)
    {
        return (new static())->where('id', $id)->where('employer_id', $agency_id)->count() > 0;
    }

    public function report()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
