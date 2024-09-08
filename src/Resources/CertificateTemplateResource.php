<?php

namespace HusamTariq\FilamentCertificateGenerator\Resources;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Support\Facades\FilamentIcon;
use HusamTariq\FilamentCertificateGenerator\Actions\Table\DownloadCertificateAction;
use HusamTariq\FilamentCertificateGenerator\Components\CertificateEditor;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use HusamTariq\FilamentCertificateGenerator\Models\CertificateTemplate;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Date;

class CertificateTemplateResource extends Resource
{
    protected static ?string $model = CertificateTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __("filament-certificate-generator::certificate-generator.resource.plural");
    }



    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __("filament-certificate-generator::certificate-generator.resource.singular");
    }

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return 'certificate-icon';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
               TextInput::make("name")->required()->label(__("filament-certificate-generator::certificate-generator.resource.certificate-name")),
               FileUpload::make("image")->image()->required()->label(__("filament-certificate-generator::certificate-generator.resource.certificate-image")),
                FileUpload::make('font')->label(__("filament-certificate-generator::certificate-generator.resource.certificate-font"))
                    ->preserveFilenames()
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table

            ->columns([

                    Tables\Columns\ImageColumn::make("image")->height(100)->label(__("filament-certificate-generator::certificate-generator.resource.certificate-image")),
                    Tables\Columns\TextColumn::make("name")->label(__("filament-certificate-generator::certificate-generator.resource.certificate-name")),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\EditAction::make("editor")->label("editor")->form([
                    TextInput::make("name")->required(),
                    CertificateEditor::make("data")->label(__("filament-certificate-generator::certificate-generator.resource.certificate-data"))
                        ->imageURL(fn($record)=>Storage::disk("public")->url($record->image))
                        ->width(850)->options([
                            "StudentArabicName"=>"اسم الطالب عربي",
                            "StudentEnglishName"=>"اسم الطالب انجليزي",
                            "TrainerArabicName"=>"اسم المدرب عربي",
                            "TrainerEnglishName"=>"اسم المدرب انجليزي",
                            "Hours"=>"Hours",
                        ])
                    ,
                ]) ,
                DownloadCertificateAction::make()->certificateName(fn($record)=>$record?->name),
                /*Tables\Actions\Action::make("rrr")->action(function ($record){
                    $defaultConfig = (new ConfigVariables())->getDefaults();
                    $fontDirs = $defaultConfig['fontDir'];
                    $defaultFontConfig = (new FontVariables())->getDefaults();
                    $fontData = $defaultFontConfig['fontdata'];

                    $size =  getimagesize(Storage::disk("public")->path($record->image));
                    $width = $size[0];
                    $height = $size[1];
                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => [$width, $height],
                        //  'orientation' => 'L',

                        'autoArabic' => true,
                        'autoLangToFont' => true,
                        'fontDir' => array_merge($fontDirs, [
                            public_path("storage"),
                        ]),
                        'fontdata' => $fontData + [
                                'din-next' => [

                                    'B' => $record->font,
                                    'useOTL' => 0x80,
                                    'useKashida' => 75,

                                ]
                            ],
                        'default_font' => 'din-next'
                    ]);

                    // $mpdf->margin_header =10;
                    $mpdf->SetDirectionality('rtl');

                    // $file =file(public_path("pdf_template/cert.jpg"));

                    $mpdf->autoArabic = true;
                    $mpdf->AddPage();
                    $mpdf->Image(Storage::disk("public")->path($record->image), 0,0,$width,$height,'jpg','',true, false);
                    //$mpdf->SetDocTemplate(public_path("pdf_template/onlineCertificate.pdf"), true);
                    //$mpdf->AddPage();
                    //  $mpdf->RoundedRect(572.7383592017738,572.7383592017738,1386,50.849999999999994,0);
                   foreach ( $record->data as $text){
                     //  $mpdf->RoundedRect($text['startY'],$text['startX'],$text['width'],$text['height'],0);
                       $mpdf->SetXY($text['startY'], $text['startX']+($text['height']/2));
                       if(!empty($text['color'])) {
                           list($r, $g, $b) = sscanf($text['color'], "#%02x%02x%02x");
                           $mpdf->SetTextColor($r, $g, $b);
                       }else {
                           $mpdf->SetTextColor(0, 0, 0);
                       }
                      // $mpdf->WriteText( $text['width'], $text['height'], "حسام الشيباني");
                       $mpdf->AutosizeText("حسام طارق عبدالرحمن الشيباني", $text['width'], "din-next", "B", $text['height']*2);

                   }
                    $mpdf->Image('http://ribu.test/api/qr', 0, 0, 500, 500, 'jpg', '', true, false);




                    return  response()->streamDownload(function () use($record,$mpdf) {

                        echo $mpdf->Output(  "serrr.pdf", "I");
                    },'ssss.pdf');

                }),*/
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->mine(filament('filament-certificate-generator')->hasAuthorScope())->orderBy("created_at","desc");
    }

    public static function getPages(): array
    {
        return [
            'index' => \HusamTariq\FilamentCertificateGenerator\Resources\CertificateTemplateResource\Pages\ManageCertificateTemplates::route('/'),
        ];
    }
}
