SELECT IF('hotelmis' IN(SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA), (SELECT major FROM hotelmis.version), 0)  AS Major;
SELECT IF('hotelmis' IN(SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA), (SELECT minor FROM hotelmis.version), 0)  AS Minor;
SELECT IF('hotelmis' IN(SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA), (SELECT patch FROM hotelmis.version), 0)  AS Patch;