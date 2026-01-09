export enum UserExperience {
    JUNIOR = 'junior',
    MIDDLE = 'middle',
    SENIOR = 'senior',
}

export function getUserExperienceLabel(experience: UserExperience): string {
    switch (experience) {
        case UserExperience.JUNIOR:
            return 'Junior';
        case UserExperience.MIDDLE:
            return 'Middle';
        case UserExperience.SENIOR:
            return 'Senior';
    }
}

export function getUserExperienceColor(experience: UserExperience): string {
    switch (experience) {
        case UserExperience.JUNIOR:
            return 'gray';
        case UserExperience.MIDDLE:
            return 'warning';
        case UserExperience.SENIOR:
            return 'success';
    }
}

export function getUserExperienceIcon(experience: UserExperience): string {
    switch (experience) {
        case UserExperience.JUNIOR:
            return 'heroicon-o-academic-cap';
        case UserExperience.MIDDLE:
            return 'heroicon-o-briefcase';
        case UserExperience.SENIOR:
            return 'heroicon-o-star';
    }
}
