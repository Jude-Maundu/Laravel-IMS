@props(['status'])

@php
$styles = match($status) {
    'Available'    => ['bg' => '#eaf3de', 'color' => '#3B6D11'],
    'Assigned'     => ['bg' => '#e6f1fb', 'color' => '#185FA5'],
    'In Use'       => ['bg' => '#EEF0FB', 'color' => '#3C3489'],
    'Under Repair' => ['bg' => '#faeeda', 'color' => '#854F0B'],
    'Damaged'      => ['bg' => '#fcebeb', 'color' => '#A32D2D'],
    'Irreparable'  => ['bg' => '#f5f5f5', 'color' => '#555'],
    'Cleaned'      => ['bg' => '#e1f5ee', 'color' => '#0F6E56'],
    'Returned'     => ['bg' => '#f0f9ff', 'color' => '#0369a1'],
    default        => ['bg' => '#f5f5f5', 'color' => '#888'],
};
@endphp

<span style="background:{{ $styles['bg'] }}; color:{{ $styles['color'] }}; font-size:10px; font-weight:600; padding:3px 10px; border-radius:20px; letter-spacing:0.04em; white-space:nowrap;">{{ $status }}</span>
