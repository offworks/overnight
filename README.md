# Overnight
An overnightly simple and consistent PHP-mysql query builder based on PDO.

## Installation
##### Composer
```
composer require offworks/overnight
```
##### Git clone
```
git clone https://github.com/offworks/overnight
```
then, require the autoload from the location
```
require_once __DIR__.'/path/to/overnight/src/autoload.php';
```

## Usage
#### Create connection
```
$db = \Overnight\Connection::create('localhost', 'user', 'password', 'dbname');
```

### SELECT
##### WHERE
Get an array of records
```
$users = $db->table('user')
        ->where('created_at > ?', array(date('Y-m-d H:i:s'))
        ->execute()->all();
```
Get an associated key value single (first) record
```
$user = $db->from('user')
        ->select('name, email, birthdate')
        ->where('user_id = ?', array($userId))
        ->execute()->first();
```
*p/s : from() is an alias of table()*

Multiple wheres
```
$tickets = $db->from('ticket')
           ->where('available = ?', array(1))
           ->where('expiry_date < ?', '2020-10-10')
           ->andWhere('seat_type = ?', array('single'))
           ->orWhere('master_ticket = ?', array(1))
           ->execute();

// will produce something like
SELECT * FROM ticket WHERE available = 1 AND expiry_date < '2020-10-10' AND seat_type = 'single'
```

##### INNER JOIN
```
$books = $db->from('book')
         ->innerJoin('author', 'author.author_id = book.author_id')
         ->execute()->all();
```
##### LEFT JOIN
```
$users = $db->from('user')
         ->leftJoin('membership', 'membership.user_id = user.user_id')
         ->execute()->all();
```
Prepared join
```
$users = $db->from('book')
        ->innerJoin('author', 'author.author_id = book.author_id AND author.is_alive = ?', array(1))
        ->execute()->all();
```

##### ORDER BY
```
$students = $db->from('student')
            ->where('is_alive = ?', array(1))
            ->orderBy('last_seen DESC')
            ->execute()->all();
```

##### GET RAW SQL
```
$sql = $db->from('news')
       ->innerJoin('editor', 'news.editor_id = editor.editor_id')
       ->where('DATE(published_at) = ?', array(date('Y-m-d H:i:s')))
       ->orderBy('published_at DESC')
       ->getSql();
```

### INSERT
```
$db->insert('user')->values(array(
    'name' => 'James',
    'like' => 'Foo cake',
    'birthdate' => '1977-05-09'
  ))->execute();
```
or
```
$db->insert('user', array(
    'name' => 'James',
    'like' => 'Foo cake',
    'birthdate' => '1977-05-09'
  ))->execute();
```

#### Last insert id
```
$userId = $db->lastInsertId();
```
or
```
$userId = $db->insert('user')->values($values)->execute()->id();
```

### UPDATE
```
$db->update('book')->where('book_id = ?', array($bookId))
 ->set(array('title' => 'the lost marble - first edition'))
 ->execute();
```

### DELETE
```
$db->delete('author')
->where('author_id = ?', array($authorId))
->execute();
```

## APIs
#### General
```
$query->execute()
  getStatement()
$query->getSql()
$db->lastInsertId()
$db->execute(string $sql, array $values, array $params))
$db->getPdo()
```

#### Select
```
from(string $table) or table(string $table)
select(string|array $columns)
where(string $condition, array $values = array())
orWhere(string $condition, array $values = array())
orderBy(string $orderBy)
groupBy(string $groupBy)
innerJoin(string $table, string $condition)
leftJoin(string $table, string $condition)
rightJoin(string $table, string $condition)
limit(int $limit, int $offset = null)
having(string $condition, array $values = array())
execute()->all()
execute()->first()
```

#### Insert
```
insert(string $table, array $values = array())
values(array $values)
execute()->id()
```

#### Update
```
update(string $table)
set(array $data)
where(string $condition, array $values = array())
orWhere(string $condition, array $values = array())
```
#### Delete
```
delete(string $table)
where(string $condition, array $values = array())
orWhere(string $condition, array $values = array())
```
