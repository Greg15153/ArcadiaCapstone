namespace WebAPI.Migrations
{
    using System;
    using System.Data.Entity.Migrations;
    
    public partial class AddSubjectToUser : DbMigration
    {
        public override void Up()
        {
            AddColumn("dbo.Users", "subject", c => c.Int(nullable: false));
        }
        
        public override void Down()
        {
            DropColumn("dbo.Users", "subject");
        }
    }
}
