<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250408221532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Вставка статичных регионов в таблицу region';
    }

    public function up(Schema $schema): void
    {
        $regions = [
            "Республика Адыгея",
            "Республика Алтай",
            "Республика Башкортостан",
            "Республика Бурятия",
            "Республика Дагестан",
            "Республика Ингушетия",
            "Кабардино-Балкарская Республика",
            "Республика Калмыкия",
            "Карачаево-Черкесская Республика",
            "Республика Карелия",
            "Республика Коми",
            "Республика Крым",
            "Республика Марий Эл",
            "Республика Мордовия",
            "Республика Саха (Якутия)",
            "Республика Северная Осетия — Алания",
            "Республика Татарстан",
            "Республика Тыва",
            "Удмуртская Республика",
            "Республика Хакасия",
            "Чеченская Республика",
            "Чувашская Республика — Чувашия",
            "Алтайский край",
            "Забайкальский край",
            "Камчатский край",
            "Краснодарский край",
            "Красноярский край",
            "Пермский край",
            "Приморский край",
            "Ставропольский край",
            "Хабаровский край",
            "Амурская область",
            "Архангельская область",
            "Астраханская область",
            "Белгородская область",
            "Брянская область",
            "Владимирская область",
            "Волгоградская область",
            "Вологодская область",
            "Воронежская область",
            "Ивановская область",
            "Иркутская область",
            "Калининградская область",
            "Калужская область",
            "Кемеровская область — Кузбасс",
            "Кировская область",
            "Костромская область",
            "Курганская область",
            "Курская область",
            "Ленинградская область",
            "Липецкая область",
            "Магаданская область",
            "Московская область",
            "Мурманская область",
            "Нижегородская область",
            "Новгородская область",
            "Новосибирская область",
            "Омская область",
            "Оренбургская область",
            "Орловская область",
            "Пензенская область",
            "Псковская область",
            "Ростовская область",
            "Рязанская область",
            "Самарская область",
            "Саратовская область",
            "Сахалинская область",
            "Свердловская область",
            "Смоленская область",
            "Тамбовская область",
            "Тверская область",
            "Томская область",
            "Тульская область",
            "Тюменская область",
            "Ульяновская область",
            "Челябинская область",
            "Ярославская область",
            "Москва",
            "Санкт-Петербург",
            "Севастополь",
            "Еврейская АО",
            "Ненецкий АО",
            "Ханты-Мансийский АО — Югра",
            "Чукотский АО",
            "Ямало-Ненецкий АО",
        ];

        foreach ($regions as $region) {
            $safeRegion = str_replace("'", "''", $region); // экранируем апострофы
            $this->addSql("INSERT INTO region (name) VALUES ('$safeRegion')");
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM region');
    }
}
