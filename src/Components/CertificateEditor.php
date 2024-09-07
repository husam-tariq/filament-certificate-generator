<?php

namespace HusamTariq\FilamentCertificateGenerator\Components;

use Filament\Forms\Components\Field;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\ValidationException;


class CertificateEditor extends Field
{
    protected  string | Closure | null $imageURl = null;

    protected int | Closure | null $width = null;

    protected array | Arrayable | Closure  $options = [];


    protected string $view = 'filament-certificate-generator::components.certificate-editor';

    public function getImageURL(): ?string
    {
       return $this->evaluate($this->imageURl);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->default( []);

        $this->dehydrateStateUsing(static function (?array $state)  {
            return collect($state ??  [])
                ->filter(static fn (?array $value, ?string $key): bool => filled($key))
                ->map(static fn (?array $value): ?array => filled($value) ? $value : [
                    'startY'=>0
                ])
                ->all();
        });


    }

    public function imageURL(string | Closure  $imageURl): static
    {
        $this->imageURl = $imageURl;
        return $this;
    }

    public function options(array | Arrayable | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): ?array
    {
        $options = $this->evaluate($this->options);

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        if (!is_array($options)|| empty($options)) {
         return throw ValidationException::withMessages(['options' => 'This options is not Array or it\'s empty']);
        }

        return $options;
    }

    public function getOptionsJson(): string
    {
        $options = $this->evaluate($this->options);

        if ($options instanceof Arrayable) {
            $options = collect($options->toArray())->toJson();
        }

        if (!is_array($options)|| empty($options)) {
            return throw ValidationException::withMessages(['options' => 'This options is not Array or it\'s empty']);
        }

        return collect($options)->toJson();
    }
    public function width(int | \Closure | null $width): static
    {
        $this->width = $width;

        return $this;
    }


    public function getWidth(): ?int
    {
        return $this->evaluate($this->width);
    }

    public function getCanvasData()
    {
        $imageURL = $this->getImageURL();
        $canvasWidth = $this->getWidth();
        $size =  getimagesize($imageURL);
        $coefficient =  $size[0]/$canvasWidth;
        return json_encode([
            "imageWidth"=>$size[0],
            "imageHeight"=>$size[1],
            "imageURL"=>$imageURL,
            "coefficient"=>$coefficient,
            "canvasWidth"=>$canvasWidth,
            "canvasHeight"=>abs($size[1]/($coefficient))
        ]);
    }
}
