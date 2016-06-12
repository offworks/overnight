# Overnight
An overnightly simple PHP-mysql query builder based on PDO.

### Installation
#### Composer
```
composer require offworks/overnight
```

### Create connection
```
$overnight = \Overnight\Connection::create('localhost', 'rut', 'pux', 'superdb');
```

### Querying
#### Simple query
Get an array of records
```
$users = $overnight->table('user')
        ->where('created_at > ?', array(date('Y-m-d H:i:s'))
        ->execute()->all();
```
Get an associated key value single (first) record
```
$user = $overnight->from('user')
        ->select('name, email, birthdate')
        ->where('user_id = ?', array($userId))
        ->execute()->first();
```
*p/s : from() is an alias of table()*

#### Joining
Inner Join
```
$books = $overnight->from('book')
         ->innerJoin('author', 'author.author_id = book.author_id')
         ->execute()->all();
```
Left Join
```
$users = $overnight->from('user')
         ->leftJoin('membership', 'membership.user_id = user.user_id')
         ->execute()->all();
```
Prepared join
```
$users = $overnight->from('book')
        ->innerJoin('author', 'author.author_id = book.author_id AND author.is_alive = ?', array(1))
        ->execute()->all();
```

#### Ordering
```
$students = $overnight->from('student')
            ->where('is_alive = ?', array(1))
            ->orderBy('last_seen DESC')
            ->execute()->all();
```

#### Get Raw Sql
```
$query = $overnight->from('news')
        ->innerJoin('editor', 'news.editor_id = editor.editor_id')
        ->where('DATE(published_at) = ?', array(date('Y-m-d H:i:s')))
        ->orderBy('published_at DESC')
        ->sql();
```

### Inserting
```
$overnight->insert('user')->values(array(
    'name' => 'James',
    'like' => 'Foo cake',
    'birthdate' => '1977-05-09'
  ))->execute();
```
or
```
$overnight->insert('user', array(
    'name' => 'James',
    'like' => 'Foo cake',
    'birthdate' => '1977-05-09'
  ))->execute();
```

#### Last insert id
```
$userId = $overnight->lastInsertId();
```

### Updating
```
$overnight->update('book')->where('book_id = ?', array($bookId))
          ->set(array('title' => 'the lost marble - first edition'))
          ->execute();
```

### Deleting
```
$overnight->delete('author')
          ->where('author_id = ?', array($authorId))
          ->execute();
```
