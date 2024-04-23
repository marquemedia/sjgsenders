@php
    $general = App\Models\GeneralSetting::first();
    $primaryRgba =  code_correction($general->primary_color);
    $secondaryRgba =  code_correction($general->secondary_color);
    $primary_light = "rgba(".$primaryRgba.",0.09)";
    $primary_light2 = "rgba(".$primaryRgba.",0.2)";
    $primary_light3 = "rgba(".$primaryRgba.",0.03)";
    $secondary_light = "rgba(".$secondaryRgba.",0.09)";
@endphp
<style>

:root{
    --font-family: "Inter", sans-serif;
  --text-primary: #071437;
  --text-secondary: #4c4f56;

  --primary-color: {{$primaryRgba }} !important;
  --primary-color-light: #f6f0ff;
  --primary-color-light-2: hsla(263, 93%, 49%, 0.2);
  --primary-color-soft: rgba(97, 9, 240, 0.12);

  --secondary-color: {{ $secondaryRgba }} !important;
  --secondary-color-light: #f8f5ff;

  --gradient-primary: linear-gradient(
    0deg,
    var(--primary-color) 0%,
    var(--secondary-color) 100%
  );

  --white: #fff;
  --dark: #080808;
  --light: #f9f9f9;
  --sitebar-bg: var(--primary-color);
  --border: #ededed;
  --input-border: #dadce0;
  --site-bg: rgb(243 243 243);
  --card-bg: #fafafa;

  --danger: #f1416c;
  --danger-light: #fff5f8;

  --success: rgb(3, 201, 136);
  --success-light: #e8fff3;

  --info: #299cdb;
  --info-light: #dcf3ff;

  --warning: #ffc700;
  --warning-light: #fff8dd;
}

</style>
