<?php // lint >= 8.0

namespace Bug11716;

use function PHPStan\Testing\assertType;

class TypeExpression
{
	/**
	 * @return '&'|'|'
	 */
	public function parse(string $glue): string
	{
		$seenGlues = ['|' => false, '&' => false];

		assertType("array{|: false, &: false}", $seenGlues);

		if ($glue !== '') {
			assertType('non-empty-string', $glue);

			\assert(isset($seenGlues[$glue]));
			$seenGlues[$glue] = true;

			assertType("'&'|'|'", $glue);
			assertType('array{|: bool, &: bool}', $seenGlues);
		} else {
			assertType("''", $glue);
		}

		assertType("''|'&'|'|'", $glue);
		assertType("array{|: bool, &: bool}", $seenGlues);

		return array_key_first($seenGlues);
	}
}

/**
 * @param array<int, string> $intKeyedArr
 * @param array<string, string> $stringKeyedArr
 */
function narrowKey($mixed, string $s, int $i, array $generalArr, array $intKeyedArr, array $stringKeyedArr): void {
	if (isset($generalArr[$mixed])) {
		assertType('mixed~(array|object|resource)', $mixed);
	} else {
		assertType('mixed', $mixed);
	}
	assertType('mixed', $mixed);

	if (isset($generalArr[$i])) {
		assertType('int', $i);
	} else {
		assertType('int', $i);
	}
	assertType('int', $i);

	if (isset($generalArr[$s])) {
		assertType('string', $s);
	} else {
		assertType('string', $s);
	}
	assertType('string', $s);

	if (isset($intKeyedArr[$mixed])) {
		assertType('mixed~(array|object|resource)', $mixed);
	} else {
		assertType('mixed', $mixed);
	}
	assertType('mixed', $mixed);

	if (isset($intKeyedArr[$i])) {
		assertType('int', $i);
	} else {
		assertType('int', $i);
	}
	assertType('int', $i);

	if (isset($intKeyedArr[$s])) {
		assertType("lowercase-string&numeric-string&uppercase-string", $s);
	} else {
		assertType('string', $s);
	}
	assertType('string', $s);

	if (isset($stringKeyedArr[$mixed])) {
		assertType('mixed~(array|object|resource)', $mixed);
	} else {
		assertType('mixed', $mixed);
	}
	assertType('mixed', $mixed);

	if (isset($stringKeyedArr[$i])) {
		assertType('int', $i);
	} else {
		assertType('int', $i);
	}
	assertType('int', $i);

	if (isset($stringKeyedArr[$s])) {
		assertType('string', $s);
	} else {
		assertType('string', $s);
	}
	assertType('string', $s);
}

/**
 * @param array<int, array<string, float>> $arr
 */
function multiDim($mixed, $mixed2, array $arr) {
	if (isset($arr[$mixed])) {
		assertType('mixed~(array|object|resource)', $mixed);
	} else {
		assertType('mixed', $mixed);
	}
	assertType('mixed', $mixed);

	if (isset($arr[$mixed]) && isset($arr[$mixed][$mixed2])) {
		assertType('mixed~(array|object|resource)', $mixed);
		assertType('mixed~(array|object|resource)', $mixed2);
	} else {
		assertType('mixed', $mixed);
	}
	assertType('mixed', $mixed);

	if (isset($arr[$mixed][$mixed2])) {
		assertType('mixed~(array|object|resource)', $mixed);
		assertType('mixed~(array|object|resource)', $mixed2);
	} else {
		assertType('mixed', $mixed);
		assertType('mixed', $mixed2);
	}
	assertType('mixed', $mixed);
	assertType('mixed', $mixed2);
}

/**
 * @param array<int, string> $arr
 */
function emptyArrr($mixed, array $arr)
{
    if (count($arr) !== 0) {
        return;
    }

    assertType('array{}', $arr);
    if (isset($arr[$mixed])) {
        assertType('mixed', $mixed);
    } else {
        assertType('mixed', $mixed);
    }
    assertType('mixed', $mixed);
}

function emptyString($mixed)
{
    // see https://3v4l.org/XHZdr
    $arr = ['' => 1, 'a' => 2];
    if (isset($arr[$mixed])) {
        assertType("''|'a'|null", $mixed);
    } else {
        assertType('mixed', $mixed); // could be mixed~(''|'a'|null)
    }
    assertType('mixed', $mixed);
}

function numericString($mixed, int $i, string $s)
{
    $arr = ['1' => 1, '2' => 2];
    if (isset($arr[$mixed])) {
        assertType("1|2|'1'|'2'|float|true", $mixed);
    } else {
        assertType('mixed', $mixed);
    }
    assertType('mixed', $mixed);

    $arr = ['0' => 1, '2' => 2];
    if (isset($arr[$mixed])) {
        assertType("0|2|'0'|'2'|float|false", $mixed);
    } else {
        assertType('mixed', $mixed);
    }
    assertType('mixed', $mixed);

	$arr = ['1' => 1, '2' => 2];
	if (isset($arr[$i])) {
		assertType("1|2", $i);
	} else {
		assertType('int', $i);
	}
	assertType('int', $i);

	$arr = ['1' => 1, '2' => 2, 3 => 3];
	if (isset($arr[$s])) {
		assertType("'1'|'2'|'3'", $s);
	} else {
		assertType('string', $s);
	}
	assertType('string', $s);

	$arr = ['1' => 1, '2' => 2, 3 => 3];
	if (isset($arr[substr($s, 10)])) {
		assertType("string", $s);
		assertType("'1'|'2'|'3'", substr($s, 10));
	} else {
		assertType('string', $s);
	}
	assertType('string', $s);
}

function intKeys($mixed)
{
    $arr = [1 => 1, 2 => 2];
    if (isset($arr[$mixed])) {
        assertType("1|2|'1'|'2'|float|true", $mixed);
    } else {
        assertType('mixed', $mixed);
    }
    assertType('mixed', $mixed);

    $arr = [0 => 0, 1 => 1, 2 => 2];
    if (isset($arr[$mixed])) {
        assertType("0|1|2|'0'|'1'|'2'|bool|float", $mixed);
    } else {
        assertType('mixed', $mixed);
    }
    assertType('mixed', $mixed);
}

function arrayAccess(\ArrayAccess $arr, $mixed) {
    if (isset($arr[$mixed])) {
        assertType("mixed", $mixed);
    } else {
        assertType('mixed', $mixed);
    }
    assertType('mixed', $mixed);
}
