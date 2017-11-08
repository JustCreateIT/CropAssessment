<?php 


class Statistics
{
/*	
	Step 1 :
Mean = Sum of X values / N(Number of values) 
= (5+10+15+20+25) / 5 
= 75 / 5 
= 15 

Step 2 :
To find the variance, 
Subtract the mean from each of the values, 
5-15 = -10 
10-15 = -5 
15-15 = 0 
20-15 = 5 
25-15 = 10 

Now square all the answers you have got from subtraction. 
(-10)2 = 100 
(-5)2 = 25 
(0)2 = 0 
(5)2 = 25 
(10)2 = 100 

Add all the Squared numbers, 
100 + 25 + 0 + 25 + 100 = 250 

Divide the sum of squares by (n-1) 
250 / (5-1) = 250 / 4 = 62.5 

Hence Variance = 62.5 

Step 3 :
Find the square root of variance, 
√62.5 = 7.905 
Hence Standard deviation is 7.905 

To find minimum and maximum SD, 
Minimum SD = Mean - SD 
= 15 - 7.905 
= 7.094 

Maximum SD = Mean + SD 
=15 + 7.905 
= 22.906 

Step 4 :
To find the population SD, 
Divide the sum of squares found in step 2 by n 
250 / 5 = 50 
Find the square root of 50, √50 = 7.07

*/
    
    public static function statsMeanOfValues(array $population)
    {
        $count = count($population);       
        
        return $mean = array_sum($population) / $count;
        
    }
    
    public static function statsCoefficentOfVariation($mean, $sd)
    {
        return round($sd/$mean, 6); 
    }
    
   
    public static function statsStandardDeviation(array $a, $sample = false) {
        
        /* The standard deviation is found by taking the 
         * square root of the average of the squared 
         * deviations of the values from their average value
         * 
         **/
		
		/* return the variance) */
		$variance = self::statsVariance($a, $sample);

        /* return the standard deviation (sqrt of the variance) */
        return sqrt($variance);
    }
	
	public static function statsVariance(array $a, $sample = false) {
        
        /* Variance is defined as the average of the squared differences from the Mean.
         * 
         **/
        
        $n = count($a);
        
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);
            return false;
        }
        if ($sample && $n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);
            return false;
        }
        
        /* get the average of the population */
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        
        /* get the deviations of each data point from 
         * the mean and square these numbers */
        foreach ($a as $val) {
            $d = ((double) $val) - $mean;
            $carry += $d * $d;
        };
        if ($sample) {
           --$n;
        }
        
        /* get the variance (mean) of these values */
        $variance = $carry / $n;

        return $variance;
    }

    
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()?{}';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
	
	public static function getCharFromNumber($i){
		return ($i > 0 && $i<27 ? chr($i+64): null);
	}
   
}
    


?>