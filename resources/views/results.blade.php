<!DOCTYPE html>
<html lang="en">
<head>
    @include('header')
</head>
<body>
<div class="container my-5">
    <h1 class="text-center mb-4">Результаты поиска</h1>

    <h2>Отели</h2>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Отель</th>
            <th>Группа отелей</th>
            <th>Цена</th>
            <th>Описание</th>
        </tr>
        </thead>
        <tbody>
        @if($response && !$response['error'])
        @foreach($response['HotelGroups'] as $hotelGroup)
            <tr>
                <td>{{ $hotelGroup['Name'] }}</td>
                <td>{{ $hotelGroup['HotelId'] }}</td>
                <td>{{ $response['TotalAccommodationPrice'] }}</td>
                <td>{{ $hotelGroup['Code'] }}</td>
            </tr>
        @endforeach
        @else
            <tr>
                <td colspan="4" class="text-center">Нет доступных номеров</td>
            </tr>
        @endif
        </tbody>
    </table>

    <div class="text-center">
        <a href="{{ route('search.form') }}" class="btn btn-secondary">Вернуться к поиску</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
