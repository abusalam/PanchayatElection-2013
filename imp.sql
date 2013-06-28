Update Office Staff Count
==========================

update PE2013_office O1 inner join 
(Select O.off_code,count(per_code) as Staffs 
from PE2013_office O LEFT JOIN 
(Select * from PE2013_personnel Where NOT Deleted) P 
ON (O.off_code=P.off_code) Group By O.off_code) P1 ON (O1.off_code =P1.off_code)
set O1.totstaff=P1.Staffs

***********************************************************************************

Update Personnel BlockCode with OfficeBlock
===========================================

update PE2013_personnel P inner join  PE2013_office O ON (O.off_code=P.off_code)
set P.BlockCode=blockmuni

***********************************************************************************

Update Personnel GradePay with Scale GradePay
=============================================

update PE2013_personnel P inner join  PE2013_scale S ON (S.scalecode=P.scalecode)
set P.gpay=S.GPay

***********************************************************************************

Show Scalewise personnel count for P0
=============================================

Select O.off_code,blockmuni,office,per_code,P.officer_nm,description,pay+P.gpay,count(*) from PE2013_office O LEFT JOIN (Select * from PE2013_personnel Where NOT Deleted and remarks='99') P ON (O.off_code=P.off_code) left join PE2013_scale S on (S.scalecode=P.scalecode) Where O.blockmuni!='0bm' and S.PostStatus='P0' group by S.scalecode,blockmuni,O.off_code
ORDER BY count(*)  DESC

***********************************************************************************

