<?php
/**
 * normal
 */
function normal($job)
{
  return $job->workload();
}

/**
 * 反转字符串
 */
function reverse($job)
{
  return strrev($job->workload());
}