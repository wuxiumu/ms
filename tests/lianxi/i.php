<?php
// 递归 尾递归 迭代 回调
// 如果一个函数中所有递归形式的调用都出现在函数的末尾，
// 我们称这个递归函数是尾递归的.
// 当递归调用是整个函数体中最后执行的语句且它的返回值不属于表达式的一部分时，
// 这个递归调用就是尾递归

// 递归
function Add($num)
{
    switch ($num) {
        case 1:
            return $num;
        default:
            return $num + Add($num - 1);
    }
}

//尾递归
function Add2($num, $result = 0)
{
    switch ($num) {
        case 0:
            return $result;
        default:
            return Add2($num - 1, $result + $num);
    }
}

//迭代
function Add3($num)
{
    $result = 0;
    while ($num != 0) {
        $result += $num;
        $num--;
    }
    return $result;
}

//回调
function AddFunc($num, $result = 0)
{
    if ($num == 0) {
        return $result;
    }
    return function () use ($num, $result) {
        return AddFunc($num - 1, $result + $num);
    };
}

function Add4($callback, $num)
{
    $result = call_user_func_array($callback, $num);
    while (is_callable($result)) {
        $result = $result();
    }
    return $result;
}

// echo Add(5);
// echo Add2(5);
// echo Add3(5);
// //echo Add3(100000)
// echo Add4('AddFunc', [100000]);
$num = 4;
$re = Add4('AddFunc', [4]);
var_dump($re);