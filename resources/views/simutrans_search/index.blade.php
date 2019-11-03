<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List</title>
</head>
<body>
    <ul>
        @foreach ($articles as $article)
            <li>{{ route('articles.show', $article->slug) }}</li>
        @endforeach
    </ul>
</body>
</html>
