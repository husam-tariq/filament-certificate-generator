<?php

namespace HusamTariq\FilamentCertificateGenerator\Actions\Table;

use Filament\Tables\Actions\Action;
use HusamTariq\FilamentCertificateGenerator\Concerns\HasCertificate;
use Illuminate\Support\Facades\Storage;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Closure;

class DownloadCertificateAction extends Action
{

    protected  string | Closure | null $certificateName = null;

    public static function getDefaultName(): ?string
    {
        return 'DownloadCertificate';
    }

    public function certificateName(string | Closure | null $certificateName): static
    {
        $this->certificateName = $certificateName;
        return $this;
    }

    public function getCertificateName(): string
    {
        $certificateName = $this->evaluate($this->certificateName);
       return (!empty($certificateName) && is_string($certificateName))?$certificateName:"certificate";
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->label(__('filament-certificate-generator::certificate-generator.actions.download-certificate'));
        $this->icon('heroicon-o-folder-arrow-down');
        $this->action(function ($record) {
            if($record instanceof HasCertificate){
                try {
               $template= $record->getCertificateTemplate();
               $certificateValues= $record->getCertificateValues();
               $defaultConfig = (new ConfigVariables())->getDefaults();
                    $fontDirs = $defaultConfig['fontDir'];
                    $defaultFontConfig = (new FontVariables())->getDefaults();
                    $fontData = $defaultFontConfig['fontdata'];
                    $size =  getimagesize(Storage::disk("public")->path($template->image));
                    $width = $size[0];
                    $height = $size[1];
                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => [$width, $height],
                        'autoArabic' => true,
                        'autoLangToFont' => true,
                        'fontDir' => array_merge($fontDirs, [
                            public_path("storage"),
                        ]),
                        'fontdata' => $fontData + [
                                'din-next' => [
                                    'B' => $template->font,
                                    'useOTL' => 0x80,
                                    'useKashida' => 75,
                                ]
                            ],
                        'default_font' => 'din-next'
                    ]);
                    $mpdf->SetDirectionality('rtl');
                    $mpdf->autoArabic = true;
                    $mpdf->AddPage();
                    $mpdf->Image(Storage::disk("public")->path($template->image), 0,0,$width,$height,'jpg','',true, false);
                   foreach ( $template->data as $key=> $item){
                       switch ($item["type"]??"") {
                           case 'text':
                               $mpdf->SetXY($item['startY'], $item['startX']+($item['height']/2));
                               if(!empty($item['color'])) {
                                   list($r, $g, $b) = sscanf($item['color'], "#%02x%02x%02x");
                                   $mpdf->SetTextColor($r, $g, $b);
                               }else {
                                   $mpdf->SetTextColor(0, 0, 0);
                               }
                               $mpdf->AutosizeText($certificateValues[$key]??"", $item['width'], "din-next", "B", $item['height']*2);
                               break;
                           case 'image':
                               $mpdf->Image($certificateValues[$key]??'http://ribu.test/api/qr', $item['startY'], $item['startX'],$item['width'],$item['height'], 'jpg', '', true, false);
                               break;
                           case 'qr':
                               $color = str_replace("#","",$item['color']??"#000000");
                               $mpdf->Image(route("certificate-generator-qr",["logo"=>base64_encode(asset('images/logo.png')),"color"=>$color ,"url"=>"http://ribu.test/admin/certificate-templates"]), $item['startY'], $item['startX'],$item['width'],$item['height'], 'jpg', '', true, false);
                               break;
                           default:
                               break;
                       }
                   }
                return  response()->streamDownload(function () use($mpdf) {
                    echo $mpdf->Output(  $this->getCertificateName().".pdf", "I");
                },$this->getCertificateName().".pdf");
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }else{

            }
            return 0;
        });


    }

}
