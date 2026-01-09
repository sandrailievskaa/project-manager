import { onMounted, onUnmounted } from 'vue';

interface Shortcut {
    key: string;
    ctrl?: boolean;
    shift?: boolean;
    alt?: boolean;
    action: () => void;
    description?: string;
}

const shortcuts: Shortcut[] = [];

export function useKeyboardShortcuts() {
    const register = (shortcut: Shortcut) => {
        shortcuts.push(shortcut);
    };
    
    const unregister = (key: string) => {
        const index = shortcuts.findIndex(s => s.key === key);
        if (index > -1) {
            shortcuts.splice(index, 1);
        }
    };
    
    const handleKeyDown = (e: KeyboardEvent) => {
        const matchingShortcut = shortcuts.find(s => {
            const keyMatch = s.key.toLowerCase() === e.key.toLowerCase();
            const ctrlMatch = !!s.ctrl === (e.ctrlKey || e.metaKey);
            const shiftMatch = !!s.shift === e.shiftKey;
            const altMatch = !!s.alt === e.altKey;
            
            return keyMatch && ctrlMatch && shiftMatch && altMatch;
        });
        
        if (matchingShortcut) {
            e.preventDefault();
            matchingShortcut.action();
        }
    };
    
    onMounted(() => {
        window.addEventListener('keydown', handleKeyDown);
    });
    
    onUnmounted(() => {
        window.removeEventListener('keydown', handleKeyDown);
    });
    
    return {
        register,
        unregister,
    };
}

