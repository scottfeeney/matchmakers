delete from api_token where userid in 
(Select userid from employer where locationId in (select locationId from location where locationName in ("Colombia","Cuba"))) or userid in (select userid from job_seeker where locationId in (select locationId from location where locationName in ("Colombia","Cuba"))) and apitokenid > 0;

delete from job_seeker_skill where jobseekerId in (select jobseekerId from job_seeker where locationId in (select locationId from location where locationName in ("Colombia","Cuba"))) and jobseekerskillid > 0;

delete from job_seeker where locationId in (select locationId from location where locationName in ("Colombia","Cuba")) and jobSeekerId > 0;

delete from job_skill where jobid in (select jobId from job where employerId in (select employerId from employer where locationId in (select locationId from location where locationName in ("Colombia","Cuba")))) and jobskillId > 0;

delete from job where employerId in (select employerId from employer where locationId in (select locationId from location where locationName in ("Colombia","Cuba"))) and jobId > 0;

delete from employer where locationId in (select locationId from location where locationName in ("Colombia","Cuba")) and employerId > 0;

delete from job where locationId in (select locationId from location where locationName in ("Colombia","Cuba")) and jobId > 0;

delete from location where locationName in ("Colombia","Cuba");




delete from api_token where userid in (select userid from job_seeker where jobTypeId in (select jobtypeId from jobtype where jobtypename in ("Dodgy","Legit"))) and apitokenid > 0;

delete from job_seeker_skill where jobseekerId in (select jobseekerId from job_seeker where jobTypeId in (select jobtypeId from jobtype where jobtypename in ("Dodgy","Legit")) and jobseekerskillid > 0;

delete from job_seeker where jobTypeId in (select jobtypeId from jobtype where jobtypename in ("Dodgy","Legit")) and jobSeekerId > 0;

delete from job_skill where jobid in (select jobId from job where jobTypeId in (select jobtypeId from jobtype where jobtypename in ("Dodgy","Legit"))) and jobskillId > 0;

delete from job where jobTypeId in (select jobtypeId from jobtype where jobtypename in ("Dodgy","Legit")) and jobId > 0;

delete from jobtype where jobtypename in ("Dodgy","Legit");




delete from job_skill where skillId in (select skillId from skill where skillCategoryId in (select skillCategoryId from skill_category where skillCategoryName in ("SomeRandomTestSkillCategory","SomeOtherTestSkillCategory","Some Category","algoTestSkillCat","!@#!@$%asdfd    1234~!@#"))) and jobskillId > 0;

delete from job_seeker_skill where skillId in (select skillId from skill where skillCategoryId in (select skillCategoryId from skill_category where skillCategoryName in ("SomeRandomTestSkillCategory","SomeOtherTestSkillCategory","Some Category","algoTestSkillCat","!@#!@$%asdfd    1234~!@#"))) and jobseekerskillId > 0;

delete from skill where skillCategoryId in (select skillCategoryId from skill_category where skillCategoryName in ("SomeRandomTestSkillCategory","SomeOtherTestSkillCategory","Some Category","algoTestSkillCat","!@#!@$%asdfd    1234~!@#")) and skillId > 0;

delete from skill_category where skillCategoryName in ("SomeRandomTestSkillCategory","SomeOtherTestSkillCategory","Some Category","algoTestSkillCat","!@#!@$%asdfd    1234~!@#");




delete from job_skill where skillId in (select skillId from skill where skillName in ("SomeSkill", "SomeOtherSkill", "Some Skill", "Looking Up", "Some test skill", "Some other test skill", "Whatever")) and jobskillId > 0;

delete from job_seeker_skill where skillId in (select skillId from skill where skillName in ("SomeSkill", "SomeOtherSkill", "Some Skill", "Looking Up", "Some test skill", "Some other test skill", "Whatever")) and jobseekerskillId > 0;

delete from skill where skillName in ("SomeSkill", "SomeOtherSkill", "Some Skill", "Looking Up", "Some test skill", "Some other test skill", "Whatever");




delete from job_skill where skillId in (select skillId from skill where createdby in (Select userId from user where email in ("anAdminPersonBeingTested@email321.com","JustTestingAnAdmin__123#@321.com","SomeAdminUserEmail@email.com","algoTestingAdminPerson@unitTests.com","someadminuserguy$#!@123.com") or modifiedBy in (Select userId from user where email in ("anAdminPersonBeingTested@email321.com","JustTestingAnAdmin__123#@321.com","SomeAdminUserEmail@email.com","algoTestingAdminPerson@unitTests.com","someadminuserguy$#!@123.com"))) and jobskillId > 0;

delete from job_seeker_skill where skillId in (select skillId from skill where createdby in (Select userId from user where email in ("anAdminPersonBeingTested@email321.com","JustTestingAnAdmin__123#@321.com","SomeAdminUserEmail@email.com","algoTestingAdminPerson@unitTests.com","someadminuserguy$#!@123.com")) or modifiedBy in (Select userId from user where email in ("anAdminPersonBeingTested@email321.com","JustTestingAnAdmin__123#@321.com","SomeAdminUserEmail@email.com","algoTestingAdminPerson@unitTests.com","someadminuserguy$#!@123.com"))) and jobseekerskillId > 0;

delete from admin_staff where userId in (select userId from user where email in ("anAdminPersonBeingTested@email321.com","JustTestingAnAdmin__123#@321.com","SomeAdminUserEmail@email.com","algoTestingAdminPerson@unitTests.com","someadminuserguy$#!@123.com")) and adminstaffId > 0;

delete from api_token where userId in (select userId from user where email in ("anAdminPersonBeingTested@email321.com","JustTestingAnAdmin__123#@321.com","SomeAdminUserEmail@email.com","algoTestingAdminPerson@unitTests.com","someadminuserguy$#!@123.com")) and apitokenid > 0;

delete from user where email in ("anAdminPersonBeingTested@email321.com","JustTestingAnAdmin__123#@321.com","SomeAdminUserEmail@email.com","algoTestingAdminPerson@unitTests.com","someadminuserguy$#!@123.com");




delete from job_skill where jobId in (Select jobId from job where jobName in ("Statue Impersonator","Widget Wrangler","Cloud Counter")) and jobskillId > 0;

delete from job where jobName in ("Statue Impersonator","Widget Wrangler","Cloud Counter") and jobskillId > 0;




delete from job_skill where jobId in (Select jobId from job where employerId in (select employerId from employer where userId in (Select userId from user where email in ("JustTestingAnEmployer__123#@321.com","JustTestingAnEmployr2__123#@321.com","someEmployerEmail@somewhere.com","algoTestingEmployerPerson@unitTests.com","jobposter@jobpostingplace.com","SomeEmployerDude@com.com.com")))) and jobskillid > 0;

delete from job where employerId in (select employerId from employer where userId in (select userId from user where email in("JustTestingAnEmployer__123#@321.com","JustTestingAnEmployr2__123#@321.com","someEmployerEmail@somewhere.com","algoTestingEmployerPerson@unitTests.com","jobposter@jobpostingplace.com","SomeEmployerDude@com.com.com"))) and jobid > 0;

delete from employer where userId in (select userId from user where email in ("JustTestingAnEmployer__123#@321.com","JustTestingAnEmployr2__123#@321.com","someEmployerEmail@somewhere.com","algoTestingEmployerPerson@unitTests.com","jobposter@jobpostingplace.com","SomeEmployerDude@com.com.com")) and employerid > 0;

delete from api_token where userId in (select userId from user where email in ("JustTestingAnEmployer__123#@321.com","JustTestingAnEmployr2__123#@321.com","someEmployerEmail@somewhere.com","algoTestingEmployerPerson@unitTests.com","jobposter@jobpostingplace.com","SomeEmployerDude@com.com.com")) and apitokenid > 0;

delete from user where email in ("JustTestingAnEmployer__123#@321.com","JustTestingAnEmployr2__123#@321.com","someEmployerEmail@somewhere.com","algoTestingEmployerPerson@unitTests.com","jobposter@jobpostingplace.com","SomeEmployerDude@com.com.com");




delete from job_seeker_skill where jobseekerid in (Select jobseekerId from jobseeker where userid in (select userId from user where email in ("JustTestingAJobSeeker__123#@321.com","algoTestingJSPerson@unitTests.com","someJobSeekerEmail@somewhere.com","I_Seek_jobs@email.com"))) and jobseekerskillid > 0;

delete from jobseeker where userid in (select userId from user where email in ("JustTestingAJobSeeker__123#@321.com","algoTestingJSPerson@unitTests.com","someJobSeekerEmail@somewhere.com","I_Seek_jobs@email.com")) and jobseekerid > 0;

delete from api_token where userId in (select userId from user where email in ("JustTestingAJobSeeker__123#@321.com","algoTestingJSPerson@unitTests.com","someJobSeekerEmail@somewhere.com","I_Seek_jobs@email.com")) and apitokenid > 0;

delete from user where email in ("JustTestingAJobSeeker__123#@321.com","algoTestingJSPerson@unitTests.com","someJobSeekerEmail@somewhere.com","I_Seek_jobs@email.com");




delete from api_token where userId in (select userId from user where email in ("unit@tester.com", "reallyunittesting@test.com")) and apitokenid > 0;

delete from user where email in ("unit@tester.com", "reallyunittesting@test.com");

