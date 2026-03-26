<!DOCTYPE html>
<html>
<head>
    <title>Mijn trainingsplatform</title>
</head>
<body>

<h1>Mijn trainingen</h1>

<form method="POST" action="/training">
    @csrf
    <input type="text" name="titel" placeholder="Naam training">
    <button type="submit">Toevoegen</button>
</form>

<h2>Lijst</h2>

<ul>
@foreach($trainings as $training)
    <li>
        {{ $training->titel }}

        <form method="POST" action="/training/{{ $training->id }}" style="display:inline;">
            @csrf
            @method('DELETE')
            <button>Verwijderen</button>
        </form>
    </li>
@endforeach
</ul>

</body>
</html>