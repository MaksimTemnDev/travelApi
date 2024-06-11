<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testMinDiscountChild()
    {
        $this->client->request(
            'POST',
            '/api/calculate',
            [],
            [],
            [],
            '{
                "age": 13, 
                "booking_date": "04.12.2024",
                "start_date": "01.10.2025"
            }'
        );

        $res = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(24300, $res['cost']);
    }

    public function testMaxDiscountChild()
    {
        $this->client->request(
            'POST',
            '/api/calculate',
            [],
            [],
            [],
            '{
                "age": 5,
                "booking_date": "04.11.2024",
                "start_date": "12.12.2025"
            }'
        );

        $res = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(5400, $res['cost']);
    }

    public function testMidDiscountOverRangeChild()
    {
        $this->client->request(
            'POST',
            '/api/calculate',
            [],
            [],
            [],
            '{
                "cost":25000,
                "age": 7,
                "booking_date": "04.12.2024",
                "start_date": "01.03.2024"
            }'
        );

        $res = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(20500, $res['cost']);
    }

    public function testMidDiscountInRangeChild()
    {
        $this->client->request(
            'POST',
            '/api/calculate',
            [],
            [],
            [],
            '{
                "cost":5000,
                "age": 7,
                "booking_date": "04.12.2024",
                "start_date": "01.03.2024"
            }'
        );

        $res = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(3500, $res['cost']);
    }

    public function testMaxDiscountAprilByPrice()
    {
        $this->client->request(
            'POST',
            '/api/calculate',
            [],
            [],
            [],
            '{
                "age": 1,
                "booking_date": "04.11.2024",
                "start_date": "01.08.2025"
            }'
        );

        $res = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(25500, $res['cost']);
    }

    public function testMaxDiscountOctoberByPrice()
    {
        $this->client->request(
            'POST',
            '/api/calculate',
            [],
            [],
            [],
            '{
                "age": 1,
                "booking_date": "09.03.2024",
                "start_date": "01.10.2025"
            }'
        );

        $res = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(25500, $res['cost']);
    }

    public function testMaxDiscountJanuaryByPrice()
    {
        $this->client->request(
            'POST',
            '/api/calculate',
            [],
            [],
            [],
            '{
                "age": 1,
                "booking_date": "09.03.2024",
                "start_date": "16.01.2025"
            }'
        );

        $res = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(25500, $res['cost']);
    }

    // тест кейсы не все реализовал для дат т.к. есть ограничение по времени, нужно добавить случаи с ограничением на 1500, комплексный тест и тест по вариациям; 7,5,3 процента для каждого случая
}