# Overnight
An overnightly simple PHP-mysql query builder based on PDO

### Installation
#### Composer
```
composer require rosengate/overnight dev-master
```

### Create connection
```
$overnight = \Overnight\Connection::create('localhost', 'rut', 'pux', 'superdb');
```

### Querying
#### Simple query
Get an array of records
```
$users = $overnight->query('user')
        ->where('created_at > ?', array(date('Y-m-d H:i:s'))
        ->execute()->all();
```
Get an associated key value record
```
$user = $overnight->query('user')
        ->select('name, email, birthdate')
        ->where('user_id = ?', array($userId))
        ->execute()->first();
```

#### Joining
```
$books = $overnight->query('book')
         ->innerJoin('author', 'author.author_id = book.author_id')
         ->execute()->all();
```

#### Ordering
```
$students = $overnight->query('student')
            ->where('is_alive = ?', array(1))
            ->orderBy('last_seen DESC')
            ->execute()->all();
```

#### Get Raw Sql
```
$query = $overnight->query('news')
        ->innerJoin('editor', 'news.editor_id = editor.editor_id')
        ->where('DATE(published_at) = ?', array(date('Y-m-d H:i:s')))
        ->orderBy('published_at DESC')
        ->getRawSql();
```

### Inserting
```
$overnight->insert('user', array(
    'name' => 'James',
    'like' => 'Foo cake',
    'birthdate' => '1977-05-09'
  ))->execute();
```

### Updating
```
$overnight->update('book')->where('book_id = ?', array($bookId))
          ->setData(array('title' => 'the lost marble - first edition'))
          ->execute();
```

### Deleting
```
$overnight->delete('author')
          ->where('author_id = ?', array($authorId))
          ->execute();
```

## Notes
There're several codes left behind.. was trying to build an active record orm, but too cumbersome to continue. So, i am going remove those and just focus this repo on query building.
