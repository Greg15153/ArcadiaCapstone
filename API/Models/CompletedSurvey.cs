namespace API.Models
{
    using System;
    using System.Collections.Generic;
    using System.ComponentModel.DataAnnotations;
    using System.ComponentModel.DataAnnotations.Schema;
    using System.Data.Entity.Spatial;

    public partial class CompletedSurvey
    {
        public int Id { get; set; }

        public int UserId { get; set; }

        public int SurveyId { get; set; }

        [Column(TypeName = "timestamp")]
        [MaxLength(8)]
        [Timestamp]
        public byte[] Date { get; set; }

        public virtual Survey Survey { get; set; }

        public virtual User User { get; set; }

        public virtual Result Result { get; set; }
    }
}
