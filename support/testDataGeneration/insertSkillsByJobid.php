<?php

    //Simple script to iterate through jobs with IDs between specified minimum and maximum
    //and add a random number (within specified range) of skills to each job from their
    //relevant skillcategory

    //As per pathing below, when executed the current working directory must be the root of the
    //project (although this script need not be in that directory)

    require_once './classes/skill.php';
    require_once './classes/job.php';


    //first, build a datastructure containing all skills for later reference
    $allSkills = array();
    
    foreach (array(1,2,3,4,5) as $curr) {
        $allSkills[$curr] = \Classes\Skill::GetSkillsBySkillCategory($curr);
    }

    //set constants
    $minJId = 146; $maxJId = 2145;
    $minSkills = 3; $maxSkills = 8;

    $jobIDs = range($minJId, $maxJId);
    //Can add code to adjust the jobIDs array here if required
    //(i.e. skip adding skills to certain jobids, add skills to other
    //jobids not covered by above range)



    //iterate through jobs

    $numjobs = 1;
    $avgSkills = 0;

    foreach ($jobIDs as $jobID) {
        //progress output
        echo ".";
        if ($numjobs % 100 == 0) {
            echo PHP_EOL.$numjobs." jobs completed.".PHP_EOL;
            echo $avgSkills/$numjobs." average skills per job".PHP_EOL;
        }
        $numjobs++;

        
        $job = new \Classes\Job($jobID);
        $numSkillsToAdd = mt_rand($minSkills, $maxSkills);
        $avgSkills += $numSkillsToAdd;

        $skillIds = "";

        //pick and add skills
        for ($i = 0; $i < $numSkillsToAdd; $i++) {
            $skillsInCat = count($allSkills[$job->skillCategoryId]);
            $newSkill = mt_rand(0,$skillsInCat-1);

            //SaveJobSkills wants a string of skillIds
            if (strlen($skillIds == 0)) {
                $skillIds = "".$allSkills[$job->skillCategoryId][$newSkill]->skillId;
            } else {
                $skillIds .= ",".$allSkills[$job->skillCategoryId][$newSkill]->skillId;
            }
        }

        //save list
        \Classes\Job::SaveJobSkills($jobID, $skillIds);

    }
    

?>
