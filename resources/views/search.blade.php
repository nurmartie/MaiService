<!DOCTYPE html>
<html lang="en">
<head>
    @include('header')
</head>
<body>
<div class="container my-5">
    <div class="card p-4 shadow-sm">
        <h1 class="text-center mb-4">Поиск номеров</h1>
        <form action="{{ route('hotels.search') }}" method="POST">
            <div class="mb-3">
                <label for="hotel" class="form-label">Выберите отель:</label>
                <select id="hotel" name="hotel_id" class="form-select" required>
                    <option value="">Выберите отель</option>
                    @foreach ($hotels as $hotel)
                        <option value="{{ $hotel->id_hotel }}">{{ $hotel->remark }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="check_in_date" class="form-label">Дата заезда:</label>
                <input type="date" id="check_in_date" name="check_in_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="check_out_date" class="form-label">Дата выезда:</label>
                <input type="date" id="check_out_date" name="check_out_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="guests" class="form-label">Количество человек:</label>
                <input type="number" id="guests" name="guests" class="form-control" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Найти</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
