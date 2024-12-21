<!DOCTYPE html>
<html lang="en">
<head>
    @include('header')
</head>
<body>
<div class="container my-5">
    <h1 class="text-center mb-4">Результаты поиска</h1>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Отель</th>
            <th>Тип номера</th>
            <th>Квота</th>
            <th>Описание</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($roomTypes as $roomType)
            <tr>
                <td>{{ $roomType->hotel->remark }}</td>
                <td>{{ $roomType->remark }}</td>
                <td>{{ $roomType->quota }}</td>
                <td>{{ $roomType->description }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Нет доступных номеров</td>
            </tr>
        @endforelse
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
