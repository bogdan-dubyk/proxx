<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core\Board;

enum FieldValue: string
{
    case FIELD_NOT_SET = 'X';
    case FIELD_CLOSED = 'closed';
    case FIELD_BOMB = 'bomb';
    case FIELD_EMPTY = ' ';
    case FIELD_ONE = '1';
    case FIELD_TWO = '2';
    case FIELD_THREE = '3';
    case FIELD_FOUR = '4';
    case FIELD_FIVE = '5';
    case FIELD_SIX = '6';
    case FIELD_SEVEN = '7';
    case FIELD_EIGHT = '8';
    case FIELD_NINE = '9';
}