# Описание

Компонент пригодится вам, если у модели ActiveRecord необходимо переводить какие-либо свойства на разные языки.

**Требования:** 
* Yii2
* PHP 5.6 и выше

# Установка
Установка через composer
```
composer require sichkarev/yii2-translatable-attributes "dev-master"
```

# Подключение
1. Создайте поле для хранения переводов с типом json (лучше всего), text или varchar
```
$this->addColumn('table','translations', $this->json()->comment('Переводы'));
```

2. Подключите компонент TranslatableComponent в секции **components** конфигурационного файла Yii2 и укажите список используемых языков:
```
    'components' => [
        /* другие компоненты */
        'translate' => [
            'class' => TranslatableComponent::class,
            'languages' => [
                Russian::class,
                Ukrainian::class,
                English::class,
            ],
            'defaultLanguage' => Russian::class
        ]
    ]
```
Для добавления нового языка реализуйте LanguageInterface у класса, заполните нужные данные и добавьте в свойство **languages**

3. Подключите TranslateActiveRecordTrait к классу модели, в которой необходимо переводить свойства
```
use TranslateActiveRecordTrait;
```

4. Подключите TranslatableBehavior, например используя **addTranslatableBehavior** (реализован в TranslateActiveRecordTrait)
5. Реализуйте у класса интерфейс TranslatableInterface и создайте метод attributeNameForTranslate().
Этот метод должен вернуть название существующего поля, созданного в п.1.
```
class TestTranslatableModel extends ActiveRecord implements TranslatableInterface
{
    public function behaviors()
    {
        return ArrayHelper::merge (
            parent::behaviors(),
            self::addTranslatableBehavior()
        );
    }

    public function attributeNameForTranslate()
    {
        return 'translations';
    }
}
```

6. В комментариях к классу укажите поля, которые имеют собственные значения для каждого языка добавив слово **@translatable**.
Свойства с переводами нужно описать для удобства работы с ними, но их можно физически не создавать (они будут созданы динамически). 
Эти свойства являются частью модели и могут участвовать в валидации и прочих прикладных задачах (например в CRUD).
Если требуются произвольные подписи, то вы можете добавить названия полей в метод **attributeLabels()**. 
```
/**
 * This is comment of class TestTranslatableModel
 * @property string  name        @translate
 * @property string  description @translate
 * 
 * Translatable properties:
 * @property string nameEn 
 * @property string nameUa
 * @property string descriptionEn
 * @property string descriptionUa
*/
```

7. Добавьте возможность редактирования поля на форме CRUD:
```
<?=$form->field($model, 'name')->widget(TranslateInput::class, [
    'className' => 'col-md-6'
]);?>
```
свойство **className** указывает имя CSS-класса, которое будет применено для блоков

# Функции:
- setContextLanguage(LanguageInterface $lang) - устанавливает языковой контекст
- clearLangContext() - очищает языковой контекст
- andFilterWhereTranslate - добавляет фильтрацию по полю
```
$query->andFilterWhereTranslate(['like', 'nameEn', '%' . $this->nameEn . '%'])
```

Например:
```
if ($promocodeType = PromocodeType::findOne(1)) {
    $promocodeType->name = 'Русский';
    $promocodeType->nameUa = 'Украинский';
    $promocodeType->setLangContext(new Ukrainian());
    echo $promocodeType->name; //Украинский
}
```

Полный пример различных возможностей представлен в атотесте **src/Tests/TranslateActiveRecordTest.php**

# Тестовый CRUD
Для понимания того как все работает в проекте есть тестовый CRUD. Для запуска тестового CRUD сделайте следующее:
1. Подключите модуль в один из ваших каталогов (backend, frontend)
```
'modules' => [

    /* другие модули */

    'translate' => [
        'class' => TranslatableCrudModule::class
    ],
]
```

2. Запустите миграцию для создания тестовой таблицы
```
php yii migrate --migrationPath=@vendor/sichkarev/yii2-translatable-attributes/src/Migrations --interactive=0
```

3. Откройте CRUD по адресу /translate/ вашего каталога

Вроде на этом всё, если что пишите комментарии, правьте код и отправляйте реквесты.