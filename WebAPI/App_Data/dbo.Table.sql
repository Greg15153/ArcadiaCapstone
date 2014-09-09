CREATE TABLE [dbo].[Table]
(
	[Id] INT NOT NULL PRIMARY KEY, 
    [Username] NCHAR(25) NOT NULL, 
    [Password] NVARCHAR(MAX) NOT NULL, 
    [Admin] INT NOT NULL DEFAULT 0
)
