<?php

explain SELECT p.id,p.store,p.address,p.zone,p.sn,m.ver,p.src,p.img,p.starttime,p.business_hours,p.longitude,p.latitude FROM place p,machine m 
        WHERE p.sn=m.sn AND p.active=1 AND ( p.latitude BETWEEN 30.11112 AND 42.44445 ) 
        AND ( p.longitude BETWEEN 118.11112 AND 150.22223 ) ORDER BY m.ver DESC LIMIT 0,15








?>