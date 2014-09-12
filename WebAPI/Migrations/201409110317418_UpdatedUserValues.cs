namespace WebAPI.Migrations
{
    using System;
    using System.Data.Entity.Migrations;
    
    public partial class UpdatedUserValues : DbMigration
    {
        public override void Up()
        {
            AlterColumn("dbo.Users", "Username", c => c.String(nullable: false, maxLength: 25, fixedLength: true));
        }
        
        public override void Down()
        {
            AlterColumn("dbo.Users", "Username", c => c.String(maxLength: 25, fixedLength: true));
        }
    }
}
