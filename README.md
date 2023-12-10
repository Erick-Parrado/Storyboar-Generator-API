# Storyboard-Generator-API

ENDPOINTS
------------------users------------------
GET:
-/API/users/*user_id*

POST:
-/API/users/

PUT:
-/API/users/*user_id*

DELETE:
-/API/users/*user_id*

----------------projects--------------------
GET:
-/API/projects/*proj_id*

POST:
-/API/projects/

PUT:
-/API/projects/*proj_id*

DELETE:
-/API/projects/*proj_id*

PATCH:
-/API/projects/*proj_id*/PIN : regenera el PIN de acceso
 
---------------team_members----------------------
GET:
-/API/teams/*proj_id*/users : obtiene los usuarios del proyecto ingresado
-/API/teams/*user_id*/projects : obtiene los proyectos del usuario ingresado
-/API/teams/*role_id*/roles : obtiene lista de roles

POST:
-/API/teams

PUT:
-/API/teams/

DELETE:
-/API/teams/

--------------------scenes-----------------
GET:
-/API/scenes/*proj_id*/project : obtiene las escenas de un proyecto
-/API/scenes/*dayT_id*/daytimes : obtiene lista de tiempos
-/API/scenes/*spac_id*/spaces : obtiene listado de espacios
-/API/scenes/*scen_number*/*proj_id* : obtiene única escena

POST:
-/API/scenes/*scen_number*/*proj_id*

PUT:
-/API/scenes/*scen_number*/*proj_id*

DELETE:
-/API/scenes/*scen_number*/*proj_id*
-------------------planes-------------------

GET:
-/API/planes/*move_id*/moves
-/API/planes/*fram_id*/framings
-/API/planes/*shot_id*/shots
-/API/planes/*scen_number*/*proj_id* : obtiene lista de los planos de una escena
-/API/planes/*plan_number*/*scen_number*/*proj_id* : obtiene un plano de una escena

POST:
-/API/planes/*scen_number*/*proj_id*

PUT:
-/API/planes/*plan_number*/*scen_number*/*proj_id*

DELETE:
-/API/planes/*plan_number*/*scen_number*/*proj_id*

-------------------login--------------
POST:
-/API/login

