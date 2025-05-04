<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  {{-- Judul dikontrol oleh <Head> di Vue --}}
  <title inertia>{{ config('app.name', 'Laravel') }}</title>

  {{-- Compile assets --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Inertia injects tags like meta, links, dll --}}
  @inertiaHead
</head>
<body class="antialiased bg-gray-100">
  {{-- Inertia sendiri yang membuat <div id="app"> dan hydrate Vue --}}
  @inertia

</body>
</html>
