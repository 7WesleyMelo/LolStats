import type { ReactNode } from "react";

export type RoleOption = {
  key: string;
  label: string;
  icon: ReactNode;
};

const iconClass = "h-4 w-4";

export const ROLE_OPTIONS: RoleOption[] = [
  {
    key: "",
    label: "Todas",
    icon: (
      <svg viewBox="0 0 16 16" className={iconClass} fill="currentColor" aria-hidden>
        <path d="M8 1.5l1.4 3.1 3.4.3-2.6 2.2.8 3.3L8 8.9l-3 1.5.8-3.3-2.6-2.2 3.4-.3L8 1.5z" />
      </svg>
    ),
  },
  {
    key: "TOP",
    label: "Top",
    icon: (
      <svg viewBox="0 0 16 16" className={iconClass} fill="none" stroke="currentColor" strokeWidth="1.5" aria-hidden>
        <path d="M3 12.5V3.5h9M4.5 4.5l7 7" />
      </svg>
    ),
  },
  {
    key: "JUNGLE",
    label: "Jungle",
    icon: (
      <svg viewBox="0 0 16 16" className={iconClass} fill="none" stroke="currentColor" strokeWidth="1.5" aria-hidden>
        <path d="M8 2.5c2 2 2 3.5 0 5.5-2-2-2-3.5 0-5.5zM4 9.5c1.5 0 2.6.5 4 2.5-2.5.4-3.8-.2-4-2.5zM12 9.5c-1.5 0-2.6.5-4 2.5 2.5.4 3.8-.2 4-2.5z" />
      </svg>
    ),
  },
  {
    key: "MIDDLE",
    label: "Middle",
    icon: (
      <svg viewBox="0 0 16 16" className={iconClass} fill="none" stroke="currentColor" strokeWidth="1.5" aria-hidden>
        <path d="M3 12.5L12.5 3M5.5 13h7.5V5.5" />
      </svg>
    ),
  },
  {
    key: "BOTTOM",
    label: "Bottom",
    icon: (
      <svg viewBox="0 0 16 16" className={iconClass} fill="none" stroke="currentColor" strokeWidth="1.5" aria-hidden>
        <path d="M3 3.5h9v9M12 11.5l-7-7" />
      </svg>
    ),
  },
  {
    key: "UTILITY",
    label: "Support",
    icon: (
      <svg viewBox="0 0 16 16" className={iconClass} fill="none" stroke="currentColor" strokeWidth="1.5" aria-hidden>
        <path d="M8 2.5v11M2.5 8h11M4.2 4.2l7.6 7.6M11.8 4.2l-7.6 7.6" />
      </svg>
    ),
  },
];

