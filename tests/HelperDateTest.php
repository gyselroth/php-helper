<?php

/**
 * Copyright (c) 2017-2020 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Tests;

use Gyselroth\Helper\HelperDate;
use PHPUnit\Framework\Constraint\IsType;
use Zend_Date;

class HelperDateTest extends HelperTestCase
{
    public function testIsDateTimeString(): void
    {
        self::assertFalse(HelperDate::isDateTimeString('2017-4-2 12:03'));

        self::assertTrue(HelperDate::isDateTimeString('2017-4-2 12:03:12'));

        self::assertTrue(HelperDate::isDateTimeString('3.9.01 09:12', 'j.n.y H:i'));
    }

    public function testIsDateString(): void
    {
        self::assertTrue(HelperDate::isDateString('31-12-2017', '-', true));

        self::assertFalse(HelperDate::isDateString('24-3-2017'));

        self::assertTrue(HelperDate::isDateString('24.3.2017', '.', true));

        self::assertFalse(HelperDate::isDateString('03-24-2017 12:05'));
    }

    public function testIsTimeString(): void
    {
        self::assertTrue(HelperDate::isTimeString('3:02:02'));

        self::assertTrue(HelperDate::isTimeString('12:02', false));
    }

    /**
     * @throws \Zend_Date_Exception
     * @throws \Zend_Locale_Exception
     */
    public function testGetCurrentDate(): void
    {
        $weekdays = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];

        $months = [
            'Januar',
            'Februar',
            'März',
            'April',
            'Mai',
            'Juni',
            'Juli',
            'August',
            'September',
            'Oktober',
            'November',
            'Dezember'
        ];

        $locale = new \Zend_Locale();

        if ('de' === $locale->getLanguage()) {
            $string =
                $weekdays[\date('w')]
                . ', ' . \date('d.')
                . ' ' . $months[\date('n') - 1]
                . ' ' . \date('Y');
        } else {
            $string = \date('l, d. F Y');
        }

        self::assertSame($string, HelperDate::getCurrentDate());
    }

    /**
     * Test HelperDate::getUnixTimestampFromDate()
     * @throws \Zend_Date_Exception
     */
    public function testGetUnixTimestampFromDate(): void
    {
        // Test: convert date-string to UNIX timestamp
        $dateString = '2015-12-31';
        $result = HelperDate::getUnixTimestampFromDate($dateString);

        self::assertThat(
            $result,
            new IsType('int')
        );

        self::assertGreaterThan(0, $result);
        self::assertEquals(strtotime($dateString), $result);

        // Test: convert dateTime-string to UNIX timestamp
        $dateString = '2015-12-31 12:30:00';
        $result = HelperDate::getUnixTimestampFromDate($dateString);

        self::assertThat(
            $result,
            new IsType('int')
        );

        self::assertGreaterThan(0, $result);
        self::assertEquals(strtotime($dateString), $result);

        // Test: convert Zend object to UNIX timestamp
        $date = new Zend_Date();
        $timestamp = $date->toValue();

        self::assertSame($timestamp, HelperDate::getUnixTimestampFromDate($date));
    }

    /**
     * Test HelperDate::getDateFromUnixTimestamp()
     * @throws \Zend_Date_Exception
     */
    public function testGetDateFromUnixTimestamp(): void
    {
        self::assertSame(
            '10:20:20',
            HelperDate::getDateFromUnixTimestamp(1498645220, HelperDate::INDEX_FORMAT_TIME_MYSQL)
        );

        self::assertEquals(
            '1498645220000',
            HelperDate::getDateFromUnixTimestamp(1498645220, HelperDate::INDEX_FORMAT_TIMESTAMP_JAVASCRIPT)
        );

        self::assertSame(
            'Wed, 28. June 2017',
            HelperDate::getDateFromUnixTimestamp(1498645220, HelperDate::INDEX_FORMAT_WEEKDAY_SHORT_DAY_MONTH_YEAR)
        );

        self::assertSame(
            'Wednesday, 28. June 2017',
            HelperDate::getDateFromUnixTimestamp(1498645220, HelperDate::INDEX_FORMAT_WEEKDAY_LONG_DAY_MONTH_YEAR)
        );
    }

    /**
     * @throws \Zend_Date_Exception
     */
    public function testGetMySqlDateTimeFromDate(): void
    {
        self::assertSame('2017-06-28 10:20:20', HelperDate::getMySqlDateTimeFromDate(1498645220));
    }

    public function testGetDateTime(): void
    {
        self::assertSame(
            '{"date":"2017-06-28 10:20:20.000000","timezone_type":1,"timezone":"+00:00"}',
            json_encode(HelperDate::getDateTime(1498645220))
        );

        self::assertSame(
            '{"date":"2017-06-28 12:20:20.000000","timezone_type":3,"timezone":"UTC"}',
            json_encode(HelperDate::getDateTime('28.6.2017, 12:20:20'))
        );
    }

    public function testGetDateStringFromDateTimeString(): void
    {
        self::assertSame('2017-05-02', HelperDate::getDateStringFromDateTimeString('2017-05-02 12:20:20'));
    }

    public function testGetTimeStringFromDateTimeString(): void
    {
        self::assertSame('12:20:20', HelperDate::getTimeStringFromDateTimeString('2017-05-02 12:20:20'));
    }

    public function testGetDateParts(): void
    {
        self::assertSame(
            '{"year":"2017","month":"6","day":"28"}',
            json_encode(HelperDate::getDateParts(1498645220))
        );

        self::markTestIncomplete('@todo: Review and correct test and rel. method');

//        self::assertSame(
//          '{"year":"17","month":"6","day":"28"}',
//          json_encode(HelperDate::getDateParts('28.6.2017', 'y', 'n', 'd'))
//        );
    }

    public function testGetDatePartsAtStartOfDay(): void
    {
        self::assertSame(
            '{"array":["1994","05","22","12","20","15"],"timestamp":769564800}',
            json_encode(HelperDate::getDatePartsAtStartOfDay('1994-05-22-12-20-15'))
        );

        self::assertSame(
            '{"array":["1994","05","22"],"timestamp":769564800}',
            json_encode(HelperDate::getDatePartsAtStartOfDay('1994-05-22'))
        );
    }

    public function testGetTimestampStartOfDay(): void
    {
        self::markTestIncomplete('@todo: Review and correct test and rel. method');

//        self::assertEquals('769564800', HelperDate::getTimestampStartOfDay('1994-05-22'));
    }

    public function testGetTimestampEndOfDay(): void
    {
        self::markTestIncomplete('@todo: Review and correct test and rel. method');

//        self::assertEquals('769651199', HelperDate::getTimestampEndOfDay('1994-05-22'));
    }

    public function testGetTimeStringParts(): void
    {
        self::assertSame(
            '{"hour":12,"minutes":5,"seconds":12}',
            json_encode(HelperDate::getTimeStringParts('12:05:12'))
        );

        self::assertSame(
            '{"hour":12,"minutes":5}',
            json_encode(HelperDate::getTimeStringParts('12:05'))
        );
    }

    public function testGetTimeString(): void
    {
        self::assertSame('10:20:20', HelperDate::getTimeString(1498645220800, true));

        self::assertSame('10:20:20', HelperDate::getTimeString(1498645220));

        self::assertSame('12:20', HelperDate::getTimeString('12:20:20', false, false, true));
    }

    public function testGetSumSecondsOfTimeParts(): void
    {
        self::assertEquals(
            '44420',
            HelperDate::getSumSecondsOfTimeParts([
            'hour'    => '12',
            'minutes' => '20',
            'seconds' => '20'
            ])
        );

            self::assertEquals(
                44420 + strtotime('today'),
                HelperDate::getSumSecondsOfTimeParts([
                'hour'    => '12',
                'minutes' => '20',
                'seconds' => '20'
                ], true)
            );
    }

    public function testGetSumMinutesOfTimeParts(): void
    {
        self::assertEquals(
            '740',
            round(HelperDate::getSumMinutesOfTimeParts([
            'hour'    => '12',
            'minutes' => '20',
            'seconds' => '20'
            ]))
        );

            self::assertEquals(
                round((44420 + strtotime('today')) / 60),
                round(HelperDate::getSumMinutesOfTimeParts([
                'hour'    => '12',
                'minutes' => '20',
                'seconds' => '20'
                ], true))
            );
    }

    public function testGetSumSecondsOfTimeString(): void
    {
        self::assertEquals('44420', HelperDate::getSumSecondsOfTimeString('12:20:20'));
    }

    public function testGetSumMinutesOfTimeString(): void
    {
        self::assertEquals('740', round(HelperDate::getSumMinutesOfTimeString('12:20:20')));
    }

    public function testGetWeekdayNumberFromTimestamp(): void
    {
        self::assertEquals('3', HelperDate::getWeekdayNumberFromTimestamp(1498645220));
    }

    /**
     * @throws \Zend_Date_Exception
     */
    public function testGetMondayOfWeek(): void
    {
        self::assertEquals('26.06.2017', HelperDate::getMondayOfWeek('2017-01-07')->toString('dd.MM.Y'));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     * @throws \Zend_Date_Exception
     */
    public function testGetDateDiff(): void
    {
        self::assertEquals('2', HelperDate::getDateDiff(new Zend_Date(1498645220), new Zend_Date(1498745220)));

        self::assertEquals(
            '27',
            HelperDate::getDateDiff(new Zend_Date(1498645220), new Zend_Date(1498745220), 'hour')
        );

        self::assertNull(HelperDate::getDateDiff(new Zend_Date(1498645220), new Zend_Date(1498745220), 'minute'));
    }

    public function testGetWeeksBetween(): void
    {
        self::markTestIncomplete('@todo: Review and correct test and rel. method');

//        self::assertSame(
//            '{"startWeek":"20","weeks":1,"endWeek":21}',
//            json_encode(HelperDate::getWeeksBetween(strtotime('17.5.2017'), strtotime('29.5.2017'))),
//            'getWeeksBetween function has several errors. See @todo for more information.'
//        );

//        self::assertSame(
//            '{"startWeek":"02","weeks":0,"endWeek":2}',
//            json_encode(HelperDate::getWeeksBetween(strtotime('13.1.2017'), strtotime('15.1.2017'))));

//        self::assertSame('{"startWeek":"02","weeks":52,"endWeek":2}',
//            json_encode(HelperDate::getWeeksBetween(strtotime('12.1.2016'), strtotime('14.1.2017'))));

//        self::assertSame('{"startWeek":"51","weeks":2,"endWeek":2}',
//            json_encode(HelperDate::getWeeksBetween(strtotime('25.12.2016'), strtotime('14.1.2017'))));
    }

    public function testGetDaysBetween(): void
    {
//        self::assertSame(2, HelperDate::getDaysBetween(strtotime('31.12.2016 12:00'),
//            strtotime('2.1.2017 0:00')), 'Should be 2, since new days start at 0:00');

        self::assertSame(2, HelperDate::getDaysBetween(
            strtotime('31.12.2016 12:00'),
            strtotime('2.1.2017 0:01')
        ));
    }

    public function testGetMonthNameByNumber(): void
    {
        self::assertSame('März', HelperDate::getMonthNameByNumber(3));

        self::assertSame('Mär', HelperDate::getMonthNameByNumber(3, true));

        self::assertSame('Mai', HelperDate::getMonthNameByNumber(strtotime('12.5.2017')));
    }

    public function testGetWeekdayNameByNumber(): void
    {
        self::assertSame('Dienstag', HelperDate::getWeekdayNameByNumber(2));

        self::assertSame('Di', HelperDate::getWeekdayNameByNumber(2, true));

        self::assertSame('Freitag', HelperDate::getWeekdayNameByNumber(strtotime('12.5.2017')));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\DateException
     */
    public function testGetIcsDateFromDateString(): void
    {
        self::assertSame('20170512', HelperDate::getIcsDateFromDateString('12.5.2017'));

        self::assertSame('20170512T123456', HelperDate::getIcsDateFromDateString('12.5.2017 12:34:56', true));

        $this->expectException('Gyselroth\Helper\Exception\DateException');

        HelperDate::getIcsDateFromDateString('125.2017');
    }

    /**
     * @throws \Gyselroth\Helper\Exception\DateException
     */
    public function testGetIcsDateTimeFromDateString(): void
    {
        self::assertSame('20170512T123456', HelperDate::getIcsDateTimeFromDateString('12.5.2017 12:34:56'));
    }

    public function testGetTimestampFirstDayOfCalendarWeek(): void
    {
        self::assertSame(1483315200, HelperDate::getTimestampFirstDayOfCalendarWeek(1, 2017));

        self::assertSame(1485129600, HelperDate::getTimestampFirstDayOfCalendarWeek(4, 17));

        self::assertSame(1452470400, HelperDate::getTimestampFirstDayOfCalendarWeek(2, 2016));
    }

    public function testGetAgeByBirthYear(): void
    {
        self::markTestSkipped(
            'Result dependent on current year: the formula needed to test this is the same'
            . ' as the function being tested'
        );
    }

    public function testGetClosestDate(): void
    {
        self::assertSame(4, HelperDate::getClosestDate(5, [2, 1, 10, 9, 8, 4]));
    }

    public function testGetDateShifted(): void
    {
        self::markTestSkipped('Function returns empty array');
    }

    /**
     * @throws \Zend_Date_Exception
     */
    public function testRenderTimerangeHumanReadable(): void
    {
        self::markTestSkipped();

        // @todo review and correct test and rel. method
//        self::assertSame('12. April 2017 bis 15. April 2017',
//            HelperDate::renderTimerangeHumanReadable('12.4.2017', '15.4.2017'));

//        self::assertSame('30. Dezember 2016 bis 3. Januar 2017',
//            HelperDate::renderTimerangeHumanReadable('30.12.2016', '3.1.2017'));

//        self::assertSame(
//          '12. April 2017 bis 15. April 2017',
//          HelperDate::renderTimerangeHumanReadable('12.4.2017', '15.4.2017', 'de')
//        );

//        self::assertSame(
//          'December 30, 2016 until January 3, 2017',
//          HelperDate::renderTimerangeHumanReadable('30.12.2016', '3.1.2017', 'en'),
//          '"bis" is not being translated to English'
//        );
    }

    public function testRemoveMeridiem(): void
    {
        self::assertSame('2', HelperDate::removeMeridiem('2pm'));

        self::assertSame('2', HelperDate::removeMeridiem('2 pm'));

        self::assertSame('12', HelperDate::removeMeridiem('12 am'));
    }

    public function testEnsureTimeStringHasSeconds(): void
    {
        self::assertSame('12:34:00', HelperDate::ensureTimeStringHasSeconds('12:34'));

        self::assertSame('12:34:56', HelperDate::ensureTimeStringHasSeconds('12:34:56'));
    }

    /**
     * @throws \Zend_Date_Exception
     * @throws \Zend_Locale_Exception
     */
    public function testConvertDateToUTC(): void
    {
        self::assertSame('20170512T000000', HelperDate::convertDateToUTC('12.5.2017'));

        self::assertSame(
            '2017-05-12 12:34:56',
            HelperDate::convertDateToUTC(
                strtotime('12.5.2017 12:34:56'),
                'yyyy-MM-dd HH:mm:ss'
            )
        );
    }

    public function testConvertDelimitedDateString(): void
    {
        self::assertSame('2017.5.12', HelperDate::convertDelimitedDateString('12-5-2017'));

        self::assertSame('2017.5.12', HelperDate::convertDelimitedDateString('12"5"2017', '"'));
    }

    public function testGetZendDatePartByType(): void
    {
        self::assertSame('mm', HelperDate::getZendDatePartByType('minUte'));

        self::assertSame('U', HelperDate::getZendDatePartByType('nothing'));
    }

    public function testGetCurrentWeekAndYear(): void
    {
        self::markTestSkipped('The function needed to test is identical');
    }
}
