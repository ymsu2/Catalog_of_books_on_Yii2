<?php

use yii\db\Migration;
use common\models\User;
use common\models\Author;
use common\models\Book;
use yii\helpers\Console;

/**
 * Class m240101_000002_seed_data
 */
class m240101_000002_seed_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Проверяем, есть ли уже данные
        $userCount = User::find()->count();
        if ($userCount > 0) {
            Console::output('Данные уже существуют. Пропускаем seed.');
            return true;
        }

        // Создаем администратора
        $admin = new User();
        $admin->username = 'admin';
        $admin->email = 'admin@example.com';
        $admin->setPassword('admin123');
        $admin->generateAuthKey();
        $admin->save(false);
        
        Console::output("Создан пользователь admin:admin123");

        // Создаем авторов
        $authorsData = [
            'Лев Толстой',
            'Фёдор Достоевский',
            'Антон Чехов',
            'Иван Тургенев',
            'Николай Гоголь',
            'Александр Пушкин',
            'Михаил Лермонтов',
            'Иван Бунин',
            'Александр Блок',
            'Сергей Есенин',
        ];

        $authors = [];
        foreach ($authorsData as $name) {
            $author = new Author();
            $author->name = $name;
            if ($author->save()) {
                $authors[] = $author;
                Console::output("Создан автор: {$name}");
            }
        }

        // Создаем книги
        $booksData = [
            ['Война и мир', 1869, '9785170801182', 'Эпический роман Льва Толстого'],
            ['Анна Каренина', 1877, '9785170825584', 'Роман о трагической любви'],
            ['Преступление и наказание', 1866, '9785170801199', 'Философский роман Достоевского'],
            ['Идиот', 1869, '9785170801205', 'Роман о князе Мышкине'],
            ['Вишнёвый сад', 1904, '9785170801212', 'Пьеса Чехова'],
            ['Дама с собачкой', 1899, '9785170801229', 'Рассказ Чехова'],
            ['Отцы и дети', 1862, '9785170801236', 'Роман Тургенева'],
            ['Мёртвые души', 1842, '9785170801243', 'Поэма Гоголя'],
            ['Евгений Онегин', 1833, '9785170801250', 'Роман в стихах Пушкина'],
            ['Герой нашего времени', 1840, '9785170801267', 'Роман Лермонтова'],
        ];

        foreach ($booksData as $index => $bookData) {
            $book = new Book();
            $book->title = $bookData[0];
            $book->year = $bookData[1];
            $book->isbn = $bookData[2];
            $book->description = $bookData[3];
            
            if ($book->save(false)) {
                // Назначаем авторов книгам
                $authorIndex = $index % count($authors);
                $book->link('authors', $authors[$authorIndex]);
                
                // Некоторым книгам назначаем несколько авторов
                if ($index < 3) {
                    $secondAuthorIndex = ($index + 1) % count($authors);
                    $book->link('authors', $authors[$secondAuthorIndex]);
                }
                
                Console::output("Создана книга: {$bookData[0]}");
            }
        }

        Console::output("Тестовые данные успешно созданы!");
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Console::output("m240101_000002_seed_data cannot be reverted.");
        return false;
    }
}