<?php

namespace HusamTariq\FilamentCertificateGenerator\Models;

use HusamTariq\FilamentCertificateGenerator\Concerns\HasCertificate;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CertificateTemplate extends Model implements HasCertificate
{
    use HasFactory;
    use HasUuids;

    protected $fillable=["name","image","author_id","author_type","data","font"];

    protected $casts=[
        "data"=>"array"
    ];

    public function scopeMine($query,$active=false) {
        if(!$active)
            return $query;
        $user =  filament()->getCurrentPanel()->auth()->user();
        return $query->where('author_type', get_class($user))->where('author_id', $user->id);
    }

    public function author(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'author_type', 'author_id');
    }

    public function getCertificateValues(): array
    {
       return[
           "StudentArabicName"=>"حسام طارق عبدالرحمن الشيباني",
           "StudentEnglishName"=>"HUSSAM TAREQ ABDULRAHMAN",
           "TrainerArabicName"=>"ادارة مشاريع احترافية",
           "TrainerEnglishName"=>"Trainer English Name",
           "Hours"=>"مروان القدسي",
       ];
    }

    public function getCertificateTemplate(): CertificateTemplate
    {
        return $this;
    }
}
